<?php
// Certifique-se de que o PHP é a primeira coisa no arquivo, sem HTML antes
session_start();
include('conexao.php');

// Verifica se o usuário está logado e se é um empreendedor
if (!isset($_SESSION['id']) || $_SESSION['tipo'] != 'empreendedor') {
    header("Location: login.php");
    exit;
}

// Recupera o ID do empreendedor da sessão
$id_empreendedor = $_SESSION['id'];
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
          

        .editar-empresa {
            all: unset;
           margin-top: 40px;
           background-color: #E8B50C;
           padding: 10px;
           font-family: 'poppins', sans-serif;
           color: #111111;
           font-weight: 600;
           border: none;
           font-size: 16px;
           width: 500px;
           height: 50px;
           cursor: pointer;
           transition: 0.3s;
        }

        .editar-empresa:hover {
            background-color: #f3cb2c;
             transform: scale(1.03);
        }
        
    </style>
</head>
<body>
    <header>
    <a href="principal-emp.php" style="all: unset;">
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
        <?php
            // Consulta para obter as empresas do empreendedor logado
            $sql = "SELECT * FROM empresas WHERE usuario_id = ?";
            $stmt = $mysqli->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $id_empreendedor);
                $stmt->execute();
                $result = $stmt->get_result();

                // Verifica se o empreendedor tem empresas cadastradas
                if ($result->num_rows > 0) {
                    echo "<center>";
                    echo "<h1>Suas Empresas</h1>";
                    echo "</center>";
                    echo "<br>";
                    echo "<br>";
                    while ($empresa = $result->fetch_assoc()) {
                        echo '<div class="empresa">';
                        echo '<h2>' . htmlspecialchars($empresa['nome']) . '</h2>';
                        echo '<a href="editar-detalhes-empresa.php?id=' . $empresa['id'] . '" class="editar-empresa">Editar Empresa</a>';
                        echo "<br>";
                        echo "<br>";
                        echo '</div>';
                    }
                } else {
                    echo "<p>Você ainda não cadastrou nenhuma empresa.</p>";
                }
                $stmt->close();
            } else {
                echo "<p>Erro ao preparar consulta: " . htmlspecialchars($mysqli->error) . "</p>";
            }
            ?>


        </div>

    </main>

</body>

<script>

</script>
</html>