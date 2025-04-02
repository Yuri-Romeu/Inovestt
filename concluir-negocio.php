<?php
session_start();
include('conexao.php');  // Assumindo que já tenha um arquivo de conexão

// Verifica se o chat_id está presente na URL
if (!isset($_GET['chat_id'])) {
    echo "Chat não encontrado.";
    exit;
}

$chat_id = $_GET['chat_id'];
$usuario_id = $_SESSION['id'];  // ID do usuário logado
$tipo_usuario = $_SESSION['tipo'];  // Tipo de usuário (investidor ou empreendedor)

// Quando o usuário clicar no botão "Concluir", atualiza o status da negociação
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica o status atual da negociação
    $query = "SELECT status_negociacao FROM chats WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $chat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $chat = $result->fetch_assoc();
    
    // Lógica para atualizar o status dependendo do tipo de usuário e status atual
    if ($chat) {
        if ($chat['status_negociacao'] == 'aberto') {
            if ($tipo_usuario == 'investidor') {
                $novo_status = 'pendente_empreendedor'; // Investidor confirma, aguarda empreendedor
            } elseif ($tipo_usuario == 'empreendedor') {
                $novo_status = 'pendente_investidor'; // Empreendedor confirma, aguarda investidor
            }
        } elseif (($chat['status_negociacao'] == 'pendente_empreendedor' && $tipo_usuario == 'empreendedor') ||
                  ($chat['status_negociacao'] == 'pendente_investidor' && $tipo_usuario == 'investidor')) {
            $novo_status = 'concluido'; // Ambos confirmaram, o negócio é concluído
        } else {
            echo "O status da negociação está em um estado inválido.";
            exit;
        }

        // Atualiza o status no banco de dados
        $query_update = "UPDATE chats SET status_negociacao = ? WHERE id = ?";
        $stmt_update = $mysqli->prepare($query_update);
        $stmt_update->bind_param("si", $novo_status, $chat_id);
        $stmt_update->execute();

        // Redireciona para a página "Aguardando Negócio" ou página de negócio concluído
        if ($novo_status == 'concluido') {
            header("Location: negocio-fechado.php?chat_id=$chat_id");
        } else {
            header("Location: aguardando-negocio.php?chat_id=$chat_id");
        }
        exit;
    } else {
        echo "Chat não encontrado.";
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Plataforma Inovestt</title>
  <style>
     @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
     @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


     body {
            font-family: Arial, sans-serif;
            background-color: transparent;
            color: #FFFFFF;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: transparent;
        }

        .container {
            text-align: center;
            background-color: #151519;
            padding: 30px 80px;
            border-radius: 27px;
            border: solid 1px #828282;
            /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); */
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            font-size: 22px;
            margin-bottom: 0px;
        }

        p {
            font-family: 'Poppins', sans-serif;
            font-weight: 300;  
            font-size: 12px;
            color: #BCBCBC;
            margin-top: 5px;
            width: 420px;
            margin-left: 10%;
            
        }

        button {
            width: 170px;
            height: 50px;
            border-radius: 8px;
            border: none;
            margin: 4px 6px;
            cursor: pointer;
            color: #151519;
            font-family: 'Poppins', sans-serif;
            font-weight: 500; 
            transition: 0.3s;
        }

        button:hover {
            width: 180px;
            height: 52px;
            font-weight: 600;
        }

        button.concluir {
            background-color: #35A715;
        }

        button.sair {
            background-color: #DF1D1D;
        }
  </style>
</head>
<body>


     <div class="container">
        <h1>TEM CERTEZA QUE DESEJA  CONCLUIR O NEGÓCIO?</h1>
        <p>Nossa plataforma não se resposabiliza por pagamentos realizados
            entre investidores e empreendedores</p>
            <form method="POST">
            <button class="concluir" type="submit">Concluir</button>
            <button class="sair" onclick="window.history.back(); return false;">Sair</button>
        </form>
      </div>


     <script>
      
      </script>
</body>
</html>
