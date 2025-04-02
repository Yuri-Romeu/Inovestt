<?php
include('conexao.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera o ID do usuário logado
    $usuario_id = $_SESSION['id'];

    // Recupera o nome do empreendedor da tabela 'empreendedor' com base no ID
    $sql_nome = "SELECT nome FROM empreendedor WHERE id = '$usuario_id'";
    $result_nome = $mysqli->query($sql_nome);
    
    if ($result_nome->num_rows > 0) {
        // Obtem o nome do empreendedor
        $row = $result_nome->fetch_assoc();
        $nome_empreendedor = $row['nome'];
    } else {
        echo "<script>alert('Erro: Empreendedor não encontrado.'); window.location.href = 'testes.html';</script>";
        exit();
    }
    
    // Sanitiza os inputs recebidos via POST
    $nome_empresa = $mysqli->real_escape_string($_POST['nome_empresa']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $website = $mysqli->real_escape_string($_POST['website']);
    $cnpj = $mysqli->real_escape_string($_POST['cnpj']);
    $detalhe = $mysqli->real_escape_string($_POST['detalhe']);
    $categoria = $mysqli->real_escape_string($_POST['categoria']);
    $tipo_empresa = $mysqli->real_escape_string($_POST['tipo_empresa']);

    
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
            $uploadFileDir = 'uploads/empresas/';
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

    // Query de inserção com o nome do empreendedor
    $sql = "INSERT INTO empresas (nome, email, website, cnpj, detalhes, categoria, tipo_empresa, usuario_id, nome_empreendedor, foto_perfil) 
            VALUES ('$nome_empresa', '$email', '$website', '$cnpj', '$detalhe', '$categoria', '$tipo_empresa', '$usuario_id', '$nome_empreendedor', '$foto_perfil')";

    // Executa a query e verifica o sucesso
    if ($mysqli->query($sql)) {
        echo "<script>alert('Sua Empresa foi cadastrada com sucesso!'); window.location.href = 'principal-emp.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar: " . $mysqli->error . "'); window.location.href = 'principal-emp.php';</script>";
    }
}


?>