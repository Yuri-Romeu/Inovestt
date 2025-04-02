<?php
session_start();
include('protect.php'); // Proteção para usuários autenticados
include('conexao.php'); // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit;
}

// Recupera informações do usuário a partir da sessão
$usuario_id = $_SESSION['id'];
$tipo_usuario = $_SESSION['tipo'];

// Busca as informações do usuário no banco
if ($tipo_usuario == 'empreendedor') {
    $query = "SELECT * FROM empreendedor WHERE id = ?";
} else if ($tipo_usuario == 'investidor') {
    $query = "SELECT * FROM investidor WHERE id = ?";
}

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc(); // Recupera os dados do usuário
} else {
    echo "Erro ao buscar dados do usuário.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Apenas atualizar os campos que foram modificados
    $nome = !empty($_POST['nome']) ? $mysqli->real_escape_string($_POST['nome']) : $user_data['nome'];
    $email = !empty($_POST['email']) ? $mysqli->real_escape_string($_POST['email']) : $user_data['email'];
    $senha = !empty($_POST['senha']) ? $mysqli->real_escape_string($_POST['senha']) : $user_data['senha']; // Verifica se a senha foi alterada
    $sexo = !empty($_POST['sexo']) ? $mysqli->real_escape_string($_POST['sexo']) : $user_data['sexo'];
    $data_nascimento = !empty($_POST['data']) ? $mysqli->real_escape_string($_POST['data']) : $user_data['data_de_nascimento'];
    $cpf = !empty($_POST['cpf']) ? $mysqli->real_escape_string($_POST['cpf']) : $user_data['cpf'];
    $cep = !empty($_POST['CEP']) ? $mysqli->real_escape_string($_POST['CEP']) : $user_data['cep'];
    $estado = !empty($_POST['estado']) ? $mysqli->real_escape_string($_POST['estado']) : $user_data['estado'];
    $bairro = !empty($_POST['bairro']) ? $mysqli->real_escape_string($_POST['bairro']) : $user_data['bairro'];
    $rua = !empty($_POST['rua']) ? $mysqli->real_escape_string($_POST['rua']) : $user_data['rua'];
    $complemento = !empty($_POST['complemento']) ? $mysqli->real_escape_string($_POST['complemento']) : $user_data['complemento'];
    $numero = !empty($_POST['numero']) ? $mysqli->real_escape_string($_POST['numero']) : $user_data['numero'];
    $nome_perfil = !empty($_POST['nome_perfil']) ? $mysqli->real_escape_string($_POST['nome_perfil']) : $user_data['nome_perfil'];
    $bibliografia = !empty($_POST['bibliografia']) ? $mysqli->real_escape_string($_POST['bibliografia']) : $user_data['bibliografia'];

    // Processa o upload da nova foto de perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
        $fileName = $_FILES['foto_perfil']['name'];
        $fileSize = $_FILES['foto_perfil']['size'];
        $fileType = $_FILES['foto_perfil']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Verifica se a extensão é permitida
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Define o caminho de upload
            $uploadFileDir = 'uploads/empreendedor/'; // Pasta onde as imagens serão armazenadas
            $dest_path = $uploadFileDir . uniqid() . '.' . $fileExtension;

            // Move o arquivo para a pasta de upload
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // Atualiza o caminho da foto no banco de dados
                if ($tipo_usuario == 'empreendedor') {
                    $update_query = "UPDATE empreendedor SET nome = ?, email = ?, senha = ?, sexo = ?, data_de_nascimento = ?, cpf = ?, cep = ?, estado = ?, bairro = ?, rua = ?, complemento = ?, numero = ?, nome_perfil = ?, bibliografia = ?, foto_perfil = ? WHERE id = ?";
                } else if ($tipo_usuario == 'investidor') {
                    $update_query = "UPDATE investidor SET nome = ?, email = ?, senha = ?, sexo = ?, data_de_nascimento = ?, cpf = ?, cep = ?, estado = ?, bairro = ?, rua = ?, complemento = ?, numero = ?, nome_perfil = ?, bibliografia = ?, foto_perfil = ? WHERE id = ?";
                }

                $stmt = $mysqli->prepare($update_query);
                $stmt->bind_param("sssssssssssssssi", $nome, $email, $senha, $sexo, $data_nascimento, $cpf, $cep, $estado, $bairro, $rua, $complemento, $numero, $nome_perfil, $bibliografia, $dest_path, $usuario_id);
            } else {
                echo "<script>alert('Erro ao mover o arquivo para o diretório de upload.');</script>";
            }
        } else {
            echo "<script>alert('Extensão de arquivo não permitida.');</script>";
        }
    } else {
        // Atualiza os dados no banco sem alterar a foto
        if ($tipo_usuario == 'empreendedor') {
            $update_query = "UPDATE empreendedor SET nome = ?, email = ?, senha = ?, sexo = ?, data_de_nascimento = ?, cpf = ?, cep = ?, estado = ?, bairro = ?, rua = ?, complemento = ?, numero = ?, nome_perfil = ?, bibliografia = ? WHERE id = ?";
        } else if ($tipo_usuario == 'investidor') {
            $update_query = "UPDATE investidor SET nome = ?, email = ?, senha = ?, sexo = ?, data_de_nascimento = ?, cpf = ?, cep = ?, estado = ?, bairro = ?, rua = ?, complemento = ?, numero = ?, nome_perfil = ?, bibliografia = ? WHERE id = ?";
        }

        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param("ssssssssssssssi", $nome, $email, $senha, $sexo, $data_nascimento, $cpf, $cep, $estado, $bairro, $rua, $complemento, $numero, $nome_perfil, $bibliografia, $usuario_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Dados atualizados com sucesso!'); window.location.href = 'editar-perfil.php';</script>"; // Substitua por sua página anterior
    } else {
        echo "<script>alert('Erro ao atualizar dados: " . $mysqli->error . "');</script>";
    }

    $stmt->close();
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
            margin-left: 270px;
            margin-top: 50px;
            border-radius: 13px;
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

        .container form input[type="text"] {
            width: 540px;
            max-width: 800px;
        }

        .container textarea {
            width: 540px;
            height: 90px;
            background-color: #121215;
            border-radius: 9px;
            padding: 15px;
            color: #fff;
            border: 1px solid #3C3C3C;
            display: block;
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

        .container button.submit-btn {
           margin-top: 40px;
           background-color: #E8B50C;
           font-family: 'poppins', sans-serif;
           color: #111111;
           font-weight: 600;
           border: none;
           font-size: 16px;
           border-radius: 15px;
           width: 700px;
           height: 50px;
           margin-left: 70px;
           cursor: pointer;
           transition: 0.3s;
        }

        .container form input[type="file"] {
            width: 130px;
            position: absolute;
            top: 35%;
            cursor: pointer;
            left: 25%;
            z-index: 2;
            opacity: 0;
        }

        .container .profile-pic img {
            position: absolute;
            top: 25%;
            cursor: pointer;
            left: 30%;
        }

        .submit-btn:hover {
            background-color: #bea304;
             transform: scale(1.03);
        }
            /* =========================parte=lateral=================== */
            .sidebar {
                width: 300px;
                float: left;
                left: 5px;
                margin-top: 10px;
                padding: 20px;
                position: fixed;
                font-family: 'Montserrat', sans-serif;
            }

            .sidebar h1 {
                font-family: 'Montserrat', sans-serif;
                color: #fff;
                font-weight: 500;
                font-size: 21px;
                margin-bottom: 10px;
            }

            .menu {
                list-style: none;
            }

            .menu-item {
                margin-bottom: 10px;
            }

            .menu-item#conta {
                margin: 15px 0;
                background-color: #1D1D25;
                padding: 10px;
                border-radius: 11px;
                position: relative;
            }

            .menu-item#conta a {
                margin-left: 30px;
            }

            .menu-item#conta a img {
                position: absolute;
                left: 13px;
                top: 12px;
            }   

            .menu-item#editar-perfil {
                margin-left: 40px;
                margin-top: -8px;
            }

            .menu-item a {
                text-decoration: none;
                color: #ffffff;
                font-size: 18px;
                font-family: 'poppins', sans-serif;
                font-weight: 300;
            }

            .menu hr {
                border: none;
                background-color: #2A2A38;
                height: 2px;
                margin-bottom: 10px;
            }

            .menu #dados-pessoais {
                margin-left: 40px;
                position: relative;
                transition: 0.3s;
            }

            .menu #dados-pessoais a img {
                position: absolute;
                top: 5px;
                left: -28px;
            }

            .menu #dados-pessoais:hover {
                background-color: #1D1D25;
                padding: 10px;
                border-radius: 11px;
            }

            .privacy-logout {
                margin-top: 230px;
            }

            .privacy, .logout {
                margin-top: 15px;
            }

            .privacy a, .logout a {
                text-decoration: none;
                color: #ffffff;
                font-size: 16px;
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
        <button style="all: unset; cursor: pointer;"><a href="principal-emp.php"> 
        <img src="<?php echo !empty($_SESSION['user_data']['foto_perfil']) ? $_SESSION['user_data']['foto_perfil'] : 'Imagens-princ/icon-perfil-menu.png'; ?>" alt="" width="45px" height="45px" style="border-radius: 50%; object-fit: cover;"></a>
        </button>
        </div>
    </header>

    <main>
        <div class="sidebar">
            <ul class="menu">
                <h1>Configurações</h1>
                <li class="menu-item" id="conta"><a href="#"><img src="Imagens-princ/user.png" alt="" width="17px">Conta</a></li>
                <li class="menu-item" id="editar-perfil"><a href="editar-perfil.php">Editar Perfil</a></li>
                <hr class="hr">
                <h1>Seus Dados Pessoais</h1>
                <li class="menu-item" id="dados-pessoais"><a href="editar-dados-pessoais.php"><img src="Imagens-princ/outra-conta.png" alt="" width="20px">Dados Pessoais</a></li>
            </ul>
        <div class="privacy-logout">
            <div class="privacy">
                <a href="#">Privacidade</a>
            </div>
            <div class="logout">
                <a href="logout.php" style="all: unset; color: #fff; cursor: pointer;">Desconectar</a>
            </div>
          </div>
        </div>

        <div class="container">
            <h2>Conta</h2>
            <p>Conta conectada no email <span class="email">invest@test.com</span></p>
            <form method="POST" enctype="multipart/form-data">
                <p style="font-weight: 400;">Alterar Senha</p>
                <input type="password" placeholder="Senha atual" name="senha" value="<?php echo htmlspecialchars($user_data['senha']); ?>" disabled>
                
                <input type="password" id="senha" name="senha" placeholder="Nova senha" required>
            
                <input type="password" id="confirm-password" placeholder="Confirmar nova senha" required>

                <hr>
                <h2>Editar Perfil</h2>

                <label for="name">Nome (Perfil)</label>
                <input type="text" placeholder="Seu nome de perfil" name="nome_perfil" value="<?php echo htmlspecialchars($user_data['nome_perfil']); ?>"><br>

                <label for="bio">Biografia</label>
                <textarea id="bio" name="bibliografia" placeholder="Descreva brevemente sobre você"><?php echo htmlspecialchars($user_data['bibliografia']); ?></textarea>

                <div class="profile-pic">
                    <input type="file" id="foto_perfil" name="foto_perfil" style="z-index: 1;"><img src="Imagens/circulo-camera.png" alt="" id="camera" class="circle-yellow">
                    <p id="text-imgfile" style="z-index: 1;">Adicionar foto de Perfil</p><br>
                </div>
                <button type="submit" class="submit-btn">Concluir</button>
            </form>
        </div>

    </main>

</body>

<script>

</script>
</html>