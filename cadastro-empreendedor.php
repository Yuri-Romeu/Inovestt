<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Tratar os campos do formulário
    $nome = $mysqli->real_escape_string($_POST['nome']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $senha = $mysqli->real_escape_string($_POST['senha']);
    $sexo = $mysqli->real_escape_string($_POST['sexo']);
    $data = $mysqli->real_escape_string($_POST['data']);
    $cpf = $mysqli->real_escape_string($_POST['cpf']);
    $CEP = $mysqli->real_escape_string($_POST['CEP']);
    $estado = $mysqli->real_escape_string($_POST['estado']);
    $bairro = $mysqli->real_escape_string($_POST['bairro']);
    $rua = $mysqli->real_escape_string($_POST['rua']);
    $complemento = $mysqli->real_escape_string($_POST['complemento']);
    $numero = $mysqli->real_escape_string($_POST['numero']);
    $nome_perfil = $mysqli->real_escape_string($_POST['nome_perfil']);
    $bibliografia = $mysqli->real_escape_string($_POST['bibliografia']);

    // Inicializa o caminho da foto como vazio
    $foto_perfil = '';

    // Verifica se o arquivo de imagem foi enviado sem erros
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
        $fileName = $_FILES['foto_perfil']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Definir extensões permitidas
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Definir o diretório onde a imagem será salva
            $uploadFileDir = 'uploads/empreendedor/';
            // Cria um nome único para a imagem, para evitar duplicidade
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            // Verifica se a pasta de upload existe, se não, cria ela
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            // Move o arquivo para o diretório 'uploads'
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Salva o caminho da imagem no banco de dados
                $foto_perfil = $mysqli->real_escape_string($dest_path);
            } else {
                echo "<script>alert('Erro ao mover o arquivo para o diretório.');</script>";
            }
        } else {
            echo "<script>alert('Extensão de arquivo não permitida. Use apenas jpg, gif, png ou jpeg.');</script>";
        }
    }

    // Inserir os dados no banco de dados, incluindo o caminho da foto (se houver)
    $sql = "INSERT INTO `empreendedor` (nome, email, senha, sexo, data_de_nascimento, cpf, cep, estado, bairro, rua, complemento, numero, nome_perfil, bibliografia, foto_perfil) VALUES 
    ('$nome', '$email', '$senha', '$sexo', '$data', '$cpf', '$CEP', '$estado', '$bairro', '$rua', '$complemento', '$numero', '$nome_perfil', '$bibliografia', '$foto_perfil')";

    if ($mysqli->query($sql)) {
        echo "<script>alert('Empreendedor cadastrado com sucesso!'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar: " . $mysqli->error . "'); window.location.href = 'cadastrar-emp.html';</script>";
    }
}
?>
