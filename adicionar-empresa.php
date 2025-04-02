<?php
session_start();

include('protect.php'); // Protege a página para usuários autenticados
include('conexao.php'); // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit;
}

// Recupera informações do usuário a partir da sessão
$usuario_id = $_SESSION['id'];
$tipo_usuario = $_SESSION['tipo'];

// Recupera todos os dados do usuário do banco de dados
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
    // Armazena todos os dados do usuário na sessão
    $_SESSION['user_data'] = $result->fetch_assoc();
} else {
    echo "Erro ao buscar dados do usuário.";
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Empresa</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        body {
            background-color: #121215;
        }

        header {
            display: flex;
            justify-content: space-around;
            align-items: center;
            background-color: #EAB81B;
            width: 100%;
            height: 70px;
        }

        header .logo a, p {
            all: unset;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            font-family: 'Montserrat', sans-serif;
            color: #161616;
        }

        header .logo img {
            transition: 0.3s;
        }

        header .logo img:hover {
            width: 80px;
        }

        header button {
            padding: 10px;
            width: 130px;
            font-size: 14px;
            height: 35px;
            background-color: #161616;
            text-align: center;
            border: none;
            border-radius: 30px;
            color: #EAB81B;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        header button:hover {
            background-color: #333333;
            color: #EAB81B;
            transform: scale(1.05);
        }

        h1#title {
            text-align: center;
            color: #f3f3f3;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 28px;
            margin: 30px 0;
        }

        main {
            margin: 0 auto;
            background-color: #16161b;
            width: 80%;
            padding: 30px;
            border: 1px solid #313131;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        main h1 {
            color: #f3f3f3;
            font-weight: 300;
            font-size: 19px;
            text-align: center;
            margin-top: -8px;
            margin-bottom: 23px;
            font-family: 'Poppins', sans-serif;
        }

        main hr.linha {
            width: 100%;
            margin: 0 auto;
            border: none;
            background-color: #121215;
            height: 5px;
            margin-bottom: 18px;
        }

        main img {
            position: absolute;
            top: 8.5%;
            left: 49.6%;
        }

       

        main h1#subtitle {
            font-family: 'Poppins', sans-serif;
            color: #f3f3f3;
            font-weight: 500;
            font-size: 15px;
            margin-bottom: 115px;
        }

        form input[type="file"] {
            cursor: pointer;
            opacity: 0;
            position: absolute;
            top: 20%;
            height: 30px;
            width: 90px;
            left: 47%;
            z-index: 1;
        }

        main .circle-yellow {
                    cursor: pointer;
                    position: absolute;
                    top: 16%;
                    left: 47%;
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    background-color: #EAB81B;
                }
                
        form #text-imgfile {
            color: #f3f3f3;
            cursor: default;
        }

        main img#camera {
            position: absolute;
            top: 17.2%;
            left: 47.779%;
        }

        form .form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        form .form label {
            color: #f3f3f3;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            display: block;
            margin-top: 17px;
            margin-bottom: 5px;
        }

        form .form input {
            margin: 5px 0;
            width: 100%;
            height: 45px;
            border-radius: 9px;
            background-color: #121215;
            border: 1px solid #3C3C3C;
            color: #F3F3F3;
            font-weight: 300;
            padding: 15px;
        }

        form .form textarea {
            margin: 5px 0;
            width: 100%;
            height: 155px;
            border-radius: 9px;
            background-color: #121215;
            border: 1px solid #3C3C3C;
            color: #F3F3F3;
            font-weight: 300;
            padding: 15px;
            margin-bottom: 10px;
        }

        form .form input[type="submit"] {
            background-color: #EAB81B;
            color: #121215;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            width: 500px;
            height: 50px;
            border-radius: 9px;
            font-size: 15px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            margin-bottom: 10px;
            margin-left: 52%;
        }

        form .form input[type="submit"]:hover {
            background-color: #e7c609;
            transform: scale(1.03);
            font-size: 16px;
        }

        .possui-conta {
            color: #f3f3f3;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            font-weight: 300;
        }

        #entrar {
            color: #FDCD38;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }

        #entrar:hover {
            text-decoration: underline;
            transform: 0.2s;
            cursor: pointer;
        }

        select {
            background-color: #1D1D25;
            border: none;
            padding: 10px;
            border-radius: 10px;
            width: 460px;
            color: #D4D4D4;
            font-weight: 300;
            font-size: 14px;
            text-align: center;
            cursor: pointer;
            margin-bottom: 5px;
        }

        .right {
            margin-left: 40px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
        <button style="all: unset; cursor: pointer;" onclick="window.history.back()"><img src="Imagens/icon-ino-black.png" alt="" width="70px">
            <p style="position: absolute; top: 4%;">Início</p></button>
        </div>

        <div class="button-entrar">
            <button><a href="login.php" style="all: unset;">Entrar</a></button>
        </div>
    </header>
    <h1 id="title">Cadastro Empresarial</h1>
    <main>
        <h1>Complete as Informações com seus Dados</h1>
        <hr class="linha">
        <img src="Imagens/circulo-amarelo-preto.png" alt="">
        <h1 id="subtitle">Dados empresariais</h1>
        <form action="cadastro-empresa.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="foto_perfil" accept="image/*">
            <div class="circle-yellow"></div>
            <img src="Imagens/camera.png" alt="" id="camera">
            <center>
                <p id="text-imgfile">Adicionar foto da Empresa</p>
            </center>
            <div class="form">
                <div class="left">
                    <label for="nome_empresa">Nome da Empresa</label>
                    <input type="text" id="nome_empresa" name="nome_empresa" placeholder="Nome Completo" required><br>

                    <label for="email">Email Profissional</label>
                    <input type="email" id="email" name="email" placeholder="Exemplo@gmail.com" required><br>

                    <label for="website">website (opcional)</label>
                    <input type="text" name="website" id="website" placeholder="https://exemplo.com"><br>

                    <label for="cnpj">CNPJ</label>
                    <input type="number" name="cnpj" id="cnpj" placeholder="00.000.000/0000-00" required><br><br>
                </div>

                <div class="right">
                    <label for="detalhe">Detalhes</label>
                    <textarea id="detalhe" name="detalhe" placeholder="Descreva um breve texto sobre sua empresa" required></textarea><br>

                    <label for="categoria">Categoria</label>
                    <select name="categoria" id="categoria" required>
                        <option value="">Selecione a categoria em que sua empresa se enquadra</option>
                        <option value="Alimentacao_Bebidas">Alimentação e Bebidas</option>
                        <option value="Financas">Finanças</option>
                        <option value="Saude">Saúde</option>
                        <option value="Tecnologia_Informacao">Tecnologia da Informação</option>
                        <option value="Varejo">Varejo</option>
                        <option value="Construcao_Civil">Construção civil</option>
                        <option value="Energia">Energia</option>
                        <option value="Educacao">Educação</option>
                        <option value="Transporte_Logistica">Transporte e Logística</option>
                        <option value="Comunicacao_Midia">Comunicação e Mídia</option>
                        <option value="Turismo_Hotelaria">Turismo e Hotelaria</option>
                        <option value="Industria">Indústria</option>
                    </select><br>

                    <label for="tipos-empresa">Tipos de Empresa</label>
                    <select name="tipo_empresa" id="tipos-empresa" required>
                        <option value="">Selecione o tipo da sua Empresa</option>
                        <option value="Empresa_Individual">Empresa Individual</option>
                        <option value="MEI">Microempreendedor Individual (MEI)</option>
                        <option value="Ltda">Sociedade Empresária Limitada (Ltda)</option>
                        <option value="Sociedade_limitada_unipessoal">Sociedade Limitada Unipessoal</option>
                        <option value="SS">Sociedade Simples (SS)</option>
                        <option value="S/A">Sociedade Anônima (S/A)</option>
                        <option value="EPP">Empresa de pequeno porte (EPP)</option>
                        <option value="Empresa_medio_grande">Empresas de médio e grande porte</option>
                    </select><br>
                </div>
                
                <input type="submit" value="Completar Cadastro">
            </div>
        </form>
    </main><br><br>


    <script>
        
    </script>
</body>
</html>
