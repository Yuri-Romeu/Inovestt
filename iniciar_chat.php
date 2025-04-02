<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id']) || !isset($_GET['empreendedor_id'])) {
    // Redirecionar se não estiver logado ou se o ID do empreendedor não estiver definido
    header('Location: login.php');
    exit;
}

$investidor_id = $_SESSION['id'];
$empreendedor_id = $_GET['empreendedor_id'];

// Verifique se já existe um chat
$query = "SELECT id FROM chats WHERE id_investidor = ? AND id_empreendedor = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $investidor_id, $empreendedor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Se já existe um chat, redirecione para a página de chat usando o chat_id
    $chat = $result->fetch_assoc();
    header("Location: chat.php?chat_id=" . $chat['id']);
    exit;
} else {
    // Caso contrário, crie um novo chat
    $query_novo_chat = "INSERT INTO chats (id_investidor, id_empreendedor) VALUES (?, ?)";
    $stmt_novo_chat = $mysqli->prepare($query_novo_chat);
    $stmt_novo_chat->bind_param("ii", $investidor_id, $empreendedor_id);
    $stmt_novo_chat->execute();
    
    // Redirecionar para a página de chat usando o novo chat_id
    $chat_id = $stmt_novo_chat->insert_id;
    header("Location: chat.php?chat_id=" . $chat_id);
    exit;
}
?>
