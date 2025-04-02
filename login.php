<?php
include('conexao.php');

if(isset($_POST['email']) || isset($_POST['senha'])) {

    if(strlen($_POST['email']) == 0) {
        echo "Preencha seu e-mail";
    } else if(strlen($_POST['senha']) == 0) {
        echo "Preencha sua senha";
    } else {

        $email = $mysqli->real_escape_string($_POST['email']);
        $senha = $mysqli->real_escape_string($_POST['senha']);

        // Verifica na tabela de empreendedores
        $sql_code_em = "SELECT * FROM `empreendedor` WHERE email = '$email' AND senha = '$senha'";
        $sql_query_em = $mysqli->query($sql_code_em) or die("Falha na execução do código SQL: " . $mysqli->error);

        // Verifica na tabela de investidores
        $sql_code_in = "SELECT * FROM `investidor` WHERE email = '$email' AND senha = '$senha'";
        $sql_query_in = $mysqli->query($sql_code_in) or die("Falha na execução do código SQL: " . $mysqli->error);

        $quantidade_em = $sql_query_em->num_rows;
        $quantidade_in = $sql_query_in->num_rows;

        if($quantidade_em == 1) {
            
            $usuario = $sql_query_em->fetch_assoc();

            if(!isset($_SESSION)) {
                session_start();
            }

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['tipo'] = 'empreendedor';

            header("Location: principal-emp.php");

        } else if($quantidade_in == 1) {
            
            $usuario = $sql_query_in->fetch_assoc();

            if(!isset($_SESSION)) {
                session_start();
            }

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['tipo'] = 'investidor';

            header("Location: principal-inv.php");

        } else {
            echo "<script>
            alert('Falha ao logar! E-mail ou senha incorretos');
            </script>";
        }

    }

}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <title>Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        * {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
    }


    body {
    width: 100%;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    }

    .container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .logo {
        margin-top: 20px;
        margin-bottom: 60px;
    }

    .logo a {
        all: unset;
        cursor: pointer;
    }

    .form-text {
        display: flex;
        flex-direction: column; 
        width: 50%;
        height: 100%;
        background-color: #16161B;
        color: #F3F3F3;
        padding: 50px;
    }

    .form-text h2 {
        padding: 20px;
        padding-left: 0;
        font-weight: 600;
        font-size: 28px;
    }

    .form-text label {
        display: block;
        font-weight: 400;
        color: #F3F3F3;
        font-size: 15px;
        margin-left: 5px;
    }

    .form-text input {
        background-color: transparent;
        border: 1px solid #F2F2F2;
        width: 500px;
        height: 40px;
        border-radius: 8px;
        padding: 7px;
        margin: 10px;
        margin-bottom: 25px;
        margin-left: -2px;
        color: #F4F4F4;
        margin-left: 3px;
    }

    .bi-eye-fill {
        color: #838383;
        position: absolute;
        top: 58.8%;
        left: 38.5%;
        cursor: pointer;
    }

    .bi-eye-slash-fill {
        color: #838383;
        position: absolute;
        top: 58.8%;
        left: 38.5%;
        cursor: pointer;
    }

    .form-text input::placeholder {
        color: #F4F4F4;
        font-weight: 300;
    }

    .form-text .esq-senha {
        margin-left: 73%;
        margin-bottom: 50px;
        margin-top: -14px;
        font-size: 12px;
        font-weight: 200;
        cursor: pointer;
    }

    .form-text .esq-senha a {
        all: unset;
    }

    .form-text input[type=submit] {
        all: unset;
        background-color: #EAB81B;
        padding: 10px;
        width: 480px;
        color: #16161B;
        text-align: center;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        margin-bottom: 60px;
        transition: all 0.3s ease; /* Transição suave */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra inicial */
    }

    .form-text input[type=submit]:hover {
        background-color: #D4A30A; /* Muda a cor de fundo */
        color: #18181b; /* Muda a cor do texto */
        transform: scale(1.05); /* Aumenta o tamanho */
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Aumenta a sombra */
    }

    .n-possui {
        font-size: 12px;
        font-weight: 300;
    }

    .n-possui span{
        color: #FDCD38;
        font-size: 12px;
        font-weight: 300;
        cursor: pointer;
    }

    .n-possui span:hover {
        text-decoration: underline #FDCD38;
    }

    .form-img {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        width: 70%;
        height: 100%;
        background-image: url(Imagens/background-login.png);
        background-size: cover;
        
    }

    .text-img {
        padding: 15px;
        margin-left: 50px;
        margin-top: -15%;
        color: #16161B;
    }

    .text-img h1 {
       
        font-size: 50px;
        font-weight: 700;
        white-space: pre;
        line-height: 110%;
        margin-bottom: 15px;
    }


    /* 
======================================PARTE IFRAME===================== */
.iframe-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 8;
}

.iframe-container iframe {
    width: 80%;
    height: 80%;
    border: none;
    border-radius: 10px;
    background-color: transparent;
}

.close-btn {
    position: absolute;
    top: 15%;
    right: 18%;
    background-color: #EAB81B;
    color: #17171D;
    border: none;
    border-radius: 5px;
    padding: 10px;
    cursor: pointer;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    transition: 0.2s;
}

.close-btn:hover {
    padding: 12px;
    transition: 0.2s;
}

/* 
======================================FIM=PARTE=IFRAME===================== */

    </style>
</head>
<body>
    <div class="container">
        <div class="form-text">
            <div class="logo">
                <a href="index.html"><img src="Imagens/Inovestt.png" alt="" width="80px"></a>
            </div>
            <h2>Entrar</h2>
            <form action="" method="POST">
                <label for="email">E-mail</label>
                <input type="email" placeholder="Digite seu Email" id="email" name="email" required>

                <label for="senha">Senha</label>
                <input type="password" placeholder="Digite sua Senha" id="senha" name="senha" required>
                <i class="bi bi-eye-fill" id="btn-senha" onclick="mostrarSenha()"></i>

                <p class="esq-senha"><a href="">Esqueci minha senha</a></p>

                <input type="submit" value="Entrar">
            </form>
            <p class="n-possui">Não Possui conta? <span><a id="open-iframe-btn" style=" all: unset;">Criar Cadastro</a></span></p>
        </div>


        <!-- IFRAME -->
<div class="iframe-container" id="iframeContainer">
    <button class="close-btn" id="closeBtn">Fechar</button>
    <iframe src="cad-escolha.html"></iframe>
</div>
<!-- FIM IFRAME -->


        <div class="form-img">
            <div class="text-img">
            <h1>Bem Vindo
De Volta!</h1>

            <p>A melhor plataforma do mercado digital que conecta Investidores e Empreendedores.</p>
            </div>
        </div>
    </div>

    <script src="script.js"></script>

    <script>
     function mostrarSenha(){
    var inputPass = document.getElementById('senha')
    var btnShowPass = document.getElementById('btn-senha')

    if(inputPass.type === 'password'){
        inputPass.setAttribute('type','text')
        btnShowPass.classList.replace('bi-eye-fill','bi-eye-slash-fill')
    }else{
        inputPass.setAttribute('type','password')
        btnShowPass.classList.replace('bi-eye-slash-fill','bi-eye-fill')
    }
}
    </script>
</body>
</html>