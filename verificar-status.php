<?php
session_start();
include('conexao.php');  // Assumindo que já tenha um arquivo de conexão

if (!isset($_GET['chat_id'])) {
    echo json_encode(['status_negociacao' => 'erro', 'message' => 'Chat ID não encontrado']);
    exit;
}

$chat_id = $_GET['chat_id'];

// Verifica o status atual do negócio
$query = "SELECT status_negociacao FROM chats WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $chat_id);
$stmt->execute();
$result = $stmt->get_result();
$chat = $result->fetch_assoc();

if ($chat) {
    echo json_encode(['status_negociacao' => $chat['status_negociacao']]);
} else {
    echo json_encode(['status_negociacao' => 'erro', 'message' => 'Chat não encontrado']);
}
?>
