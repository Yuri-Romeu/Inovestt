<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id']) || !isset($_POST['chat_id']) || !isset($_POST['mensagem'])) {
    // Redirecionar se não estiver logado ou se as variáveis não estiverem definidas
    header('Location: login.php');
    exit;
}

$remetente_id = $_SESSION['id'];
$chat_id = $_POST['chat_id'];
$mensagem = $_POST['mensagem'];

// Verifique se a mensagem não está vazia
if (!empty(trim($mensagem))) {
    $query = "INSERT INTO mensagens (id_chat, id_remetente, remetente_tipo, mensagem, data_envio) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $mysqli->prepare($query);
    
    // Determine o tipo de remetente (investidor ou empreendedor)
    $remetente_tipo = $_SESSION['tipo']; // 'investidor' ou 'empreendedor'
    
    $stmt->bind_param("iiss", $chat_id, $remetente_id, $remetente_tipo, $mensagem);
    $stmt->execute();
}

// Redirecionar de volta para a página do chat
header("Location: chat.php?chat_id=" . $chat_id);
exit;
?>
