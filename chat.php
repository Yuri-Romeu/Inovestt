<?php
session_start();
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    echo "Você precisa estar logado para acessar o chat.";
    exit;
}

$usuario_id = $_SESSION['id'];
$tipo_usuario = $_SESSION['tipo']; // 'investidor' ou 'empreendedor'

// Verifica se o 'chat_id' está presente na URL
if (isset($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];
} else if (isset($_GET['empreendedor_id'])) {
    // Se o 'chat_id' não está presente, verificamos se o 'empreendedor_id' está disponível para criar o chat
    $empreendedor_id = $_GET['empreendedor_id'];

    // Verifica se o chat já existe entre o usuário logado e o empreendedor
    $query_chat = "SELECT id FROM chats WHERE (id_empreendedor = ? AND id_investidor = ?) OR (id_empreendedor = ? AND id_investidor = ?)";
    $stmt_chat = $mysqli->prepare($query_chat);
    $stmt_chat->bind_param("iiii", $empreendedor_id, $usuario_id, $usuario_id, $empreendedor_id);
    $stmt_chat->execute();
    $result_chat = $stmt_chat->get_result();

    if ($result_chat->num_rows > 0) {
        // Se o chat já existe, usamos o ID existente
        $chat_row = $result_chat->fetch_assoc();
        $chat_id = $chat_row['id'];
    } else {
        // Se o chat não existe, criamos um novo
        $query_novo_chat = "INSERT INTO chats (id_empreendedor, id_investidor) VALUES (?, ?)";
        $stmt_novo_chat = $mysqli->prepare($query_novo_chat);
        $stmt_novo_chat->bind_param("ii", $empreendedor_id, $usuario_id);
        $stmt_novo_chat->execute();
        $chat_id = $stmt_novo_chat->insert_id; // ID do novo chat criado
    }

    // Redireciona para o chat usando o novo 'chat_id'
    header("Location: chat.php?chat_id=" . $chat_id);
    exit;
} else {
    echo "Erro: Chat não encontrado.";
    exit;
}

// A partir daqui, o 'chat_id' já estará definido e podemos continuar com o processamento normal

// Processamento de envio de mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'])) {
    $mensagem = trim($_POST['mensagem']);
    
    if (!empty($mensagem)) {
        // Insere a mensagem no banco de dados
        $query_mensagem = "INSERT INTO mensagens (id_chat, mensagem, remetente_tipo, id_remetente, data_envio) VALUES (?, ?, ?, ?, NOW())";
        $stmt_mensagem = $mysqli->prepare($query_mensagem);
        $stmt_mensagem->bind_param("issi", $chat_id, $mensagem, $tipo_usuario, $usuario_id);
        if ($stmt_mensagem->execute()) {
            // Redireciona de volta para o chat após enviar a mensagem
            header("Location: chat.php?chat_id=" . $chat_id);
            exit;
        } else {
            echo "Erro ao enviar mensagem: " . $stmt_mensagem->error;
        }
        $stmt_mensagem->close(); // Fecha a declaração após uso
    } else {
        echo "A mensagem não pode estar vazia.";
    }
}

// Fecha as conexões preparadas
if (isset($stmt)) {
    $stmt->close();
}
if (isset($stmt_user)) {
    $stmt_user->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações de Perfil</title>
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');
         @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }

        body {
            font-family: 'poppins', sans-serif;
            background-color: #141418;
            height: 120vh;
        }

        /* ===========================menu==============  */

        header {
            background-color: #EAB81B;
            height: 110px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            gap: 20px;
        }


        .voltar {
            background-color: #232333;
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .voltar:hover {
            width: 52px;
            height: 52px;
        }

        .voltar img {
            transition: 0.2s;
        }

        .voltar img:hover {
            width: 18px;
        }

        .pesquisa {
            position: relative;
        }

        .pesquisa input[type="text"] {
           width: 800px;
           padding: 11px;
           height: 45px;
           color: #fff;
           border-radius: 11px;
           border: none;
           background-color: #232333; 
           font-family: 'poppins', sans-serif;
           font-size: 16px;
           font-weight: 400;
        }

        .pesquisa img {
            position: absolute;
            right: 1.5%;
            bottom: 28%;
        }

        /* ===========================fim menu==============  */

        /* =====================parte=principal=============== */

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }

        .container {
            background-color: #1D1D25;
            width: 900px;
            font-family: 'Montserrat', sans-serif;
            color: #fff;
            height: 570px;
            float: right;
            padding: 40px;
            margin-top: 50px;
            border-radius: 13px;
            overflow: auto;
            position: relative;
        }



        .container h2 {
            font-weight: 500;
            margin-bottom: 12px;
        }

        .container p {
            color: #fff;
            font-weight: 200;
            margin-bottom: 10px;
        }

        .container p span.email{
            font-weight: 500;
        }

        .container form input {
            max-width: 290px;
            height: 45px;
            border-radius: 9px;
            background-color: #121215;
            border: 1px solid #3C3C3C;
            color: #fff;
            font-weight: 400;
            padding: 10px;
            margin-bottom: 15px;
        }

        .container form input[type="password"] {
            margin-right: 15px;
            width: 250px;
        }

        .container hr {
                border: none;
                background-color: #2A2A38;
                height: 2px;
                margin-bottom: 10px;
                width: 350px;
            }

        .container label {
            color: #fff;
            display: block;
            font-weight: 300;
            margin-bottom: 10px;  
            font-size: 16px; 
        }

        .container form {
            position: relative;
        }

        .container form input[type="text"] {
            width: 800px;
            max-width: 900px;
           position: static;
           bottom: 10px;
            margin-bottom: -10px
        }

        .container textarea {
            width: 850px;
            height: 50px;
            background-color: #121215;
            border-radius: 9px;
            padding: 15px;
            color: #fff;
            border: 1px solid #3C3C3C;
            display: block;
            margin-top: 190px;
        }

        .container .profile-pic {
            background-color: #121215;
            width: 250px;
            height: 180px;
            padding: 15px;
            margin-top: -180px;
            border-radius: 9px;
            float: right;
            position: relative;
        }


        .data-envio {
            font-size: 10px;
        }

        .text-mensagem {
            background: #18181E;
            padding: 10px;
            border-radius: 8px;
        }
        
    </style>
</head>
<body>
    <header>
        <button style="all: unset;" onclick="window.history.back()">
        <div class="voltar">
            <img src="Imagens-princ/setinha-voltar.png" alt="seta voltar" width="14px">
        </div>
        </button>

        <div class="pesquisa">
            <input type="text" placeholder="Procurar Empresa">
            <img src="Imagens-princ/icon-lupa.png" alt="" width="20px">
        </div>

        <div class="icon-perfil">
        <button style="all: unset; cursor: pointer;"><a> 
        <img id="img-perfil" src="<?php echo !empty($_SESSION['user_data']['foto_perfil']) ? $_SESSION['user_data']['foto_perfil'] : 'Imagens-princ/icon-perfil-menu.png'; ?>" alt="" width="45px" height="45px" style="border-radius: 50%; object-fit: cover;"></a>
        </button>
        </div>
    </header>

    <main>
        <div class="container">
            <?php 
                

                // Exibe as mensagens do chat
                $query = "SELECT m.mensagem, m.remetente_tipo, m.id_remetente, m.data_envio 
                FROM mensagens m 
                WHERE m.id_chat = ? ORDER BY m.data_envio";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("i", $chat_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                // Fecha o statement anterior antes de abrir um novo
                if (isset($stmt_user)) {
                $stmt_user->close();
                }

                // Buscar nome do remetente baseado no remetente_tipo
                if ($row['remetente_tipo'] == 'investidor') {
                $query_user = "SELECT nome FROM investidor WHERE id = ?";
                } else {
                $query_user = "SELECT nome FROM empreendedor WHERE id = ?";
                }

                $stmt_user = $mysqli->prepare($query_user);
                $stmt_user->bind_param("i", $row['id_remetente']);
                $stmt_user->execute();
                $stmt_user->bind_result($nome_remetente);
                $stmt_user->fetch();

                echo "<p class='text-mensagem'><strong>" . htmlspecialchars($nome_remetente) . ":</strong> " . htmlspecialchars($row['mensagem']) . " <em class='data-envio'>(" . $row['data_envio'] . ")</em></p>";
                }

                $mysqli->close();
            ?>



                        <!-- Formulário para envio de mensagens -->
            <form method="POST" action="chat.php?chat_id=<?php echo $chat_id; ?>">
                <input type="text" name="mensagem" placeholder="Digite sua mensagem..." required></input>
            </form>

        </div>

    </main>

</body>

<script>

</script>
</html>

