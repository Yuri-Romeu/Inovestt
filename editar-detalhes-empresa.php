<?php
session_start();
include('protect.php'); // Protege a página para usuários autenticados
include('conexao.php'); // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit;
}

// Recupera o ID da empresa via GET
if (!isset($_GET['id'])) {
    echo "ID da empresa não fornecido!";
    exit;
}

$empresa_id = $_GET['id'];

// Consulta para recuperar os dados da empresa
$query = "SELECT nome, cnpj, detalhes, categoria, tipo_empresa, website, foto_perfil FROM empresas WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Dados da empresa
    $empresa = $result->fetch_assoc();
} else {
    echo "Empresa não encontrada!";
    exit;
}

$stmt->close();

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $cnpj = $_POST['cnpj'];
    $detalhes = $_POST['detalhes'];
    $categoria = $_POST['categoria'];
    $tipo_empresa = $_POST['tipo_empresa'];
    $website = $_POST['website'];
    $foto_perfil = $empresa['foto_perfil']; // Foto atual

    // Verifica se uma nova foto foi enviada
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto_perfil']['tmp_name'];
        $file_name = $_FILES['foto_perfil']['name'];
        $file_size = $_FILES['foto_perfil']['size'];
        $file_error = $_FILES['foto_perfil']['error'];

        // Define o diretório de destino para o upload
        $upload_dir = 'uploads/empresas/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Cria o diretório se não existir
        }

        // Gera um nome único para a imagem para evitar conflitos
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid('', true) . "." . $file_ext;

        // Verifica o tipo de arquivo (você pode adicionar mais tipos permitidos se necessário)
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array(strtolower($file_ext), $allowed_extensions)) {
            // Move o arquivo para o diretório de destino
            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                $foto_perfil = $upload_dir . $new_file_name; // Atualiza o caminho da nova foto
            } else {
                echo "Erro ao fazer upload da imagem!";
            }
        } else {
            echo "Formato de imagem não permitido!";
        }
    }

    // Atualiza os dados da empresa no banco de dados
    $update_query = "UPDATE empresas SET nome=?, cnpj=?, detalhes=?, categoria=?, tipo_empresa=?, website=?, foto_perfil=? WHERE id=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("sssssssi", $nome, $cnpj, $detalhes, $categoria, $tipo_empresa, $website, $foto_perfil, $empresa_id);

    if ($update_stmt->execute()) {
        // Redireciona após a atualização bem-sucedida
        header("Location: editar-empresa.php");
        exit;
    } else {
        echo "Erro ao atualizar a empresa!";
    }
    $update_stmt->close();
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
            height: auto;
            float: right;
            padding: 40px;
            margin-top: 50px;
            border-radius: 13px;
            /* overflow: auto; */
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
            height: 160px;
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
          

        textarea {
            width: 700px;
        }
        
    </style>
</head>
<body>
    <header>
        <a href="editar-empresa.php" style="all: unset;">
        <button style="all: unset;">
        <div class="voltar">
            <img src="Imagens-princ/setinha-voltar.png" alt="seta voltar" width="14px">
        </div>
        </button></a>

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
        <div class="container">
        <h2>Editar Dados da Empresa</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="nome">Nome da Empresa:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($empresa['nome']); ?>" required>

            <label for="cnpj">CNPJ:</label>
            <input type="text" id="cnpj" name="cnpj" value="<?php echo htmlspecialchars($empresa['cnpj']); ?>" required>

            <label for="detalhes">Detalhes:</label>
            <textarea id="detalhes" name="detalhes" required ><?php echo htmlspecialchars($empresa['detalhes']); ?></textarea><br>

            <label for="categoria">Categoria:</label>
            <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($empresa['categoria']); ?>" required>

            <label for="tipo_empresa">Tipo da Empresa:</label>
            <input type="text" id="tipo_empresa" name="tipo_empresa" value="<?php echo htmlspecialchars($empresa['tipo_empresa']); ?>" required>

            <label for="website">Website:</label>
            <input type="text" id="website" name="website" value="<?php echo htmlspecialchars($empresa['website']); ?>" required><br>

            <label for="foto_perfil">Foto de Perfil:</label>
            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" value="<?php echo htmlspecialchars($empresa['foto_perfil']); ?>" required><br>
            <center>
            <button type="submit" class="submit-btn">Salvar Alterações</button></center>
        </form>

        </div>

    </main>

</body>

<script>

</script>
</html>