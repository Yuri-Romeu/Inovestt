<?php
session_start();
include('conexao.php');

// Verifica se o chat_id foi enviado via POST ou GET
if (isset($_POST['chat_id'])) {
    $chat_id = $_POST['chat_id'];
} else {
    // Se o chat_id não foi enviado via POST, tenta obtê-lo via GET
    if (isset($_GET['chat_id'])) {
        $chat_id = $_GET['chat_id'];
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

        .loader {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 80px;
        }

        .loader-dot {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #333;
        margin: 0 10px;
        animation: loader-dot 1.5s infinite ease-in-out;
        animation-delay: calc(0.5s);
        }

        .loader-dot:nth-child(2) {
        animation-delay: calc(0.5s + 0.25s);
        }

        .loader-dot:nth-child(3) {
        animation-delay: calc(0.5s + 0.5s);
        }

        @keyframes loader-dot {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.5);
        }
        100% {
            transform: scale(1);
        }
        }
  </style>
</head>
<body>


     <div class="container">
        <h1>AGUARDANDO CONCLUSÃO DA OUTRA PARTE</h1>
        <p>Nossa plataforma não se resposabiliza por pagamentos realizados
            entre investidores e empreendedores</p>
            <div class="loader">
                <div class="loader-dot"></div>
                <div class="loader-dot"></div>
                <div class="loader-dot"></div>
              </div>
      </div>


     <script>
   // Verifica periodicamente o status da negociação
   setInterval(function() {
            fetch('verificar-status.php?chat_id=<?php echo $chat_id; ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.status_negociacao === 'concluido') {
                        // Redireciona para a página de negócio fechado
                        window.location.href = 'negocio-fechado.php?chat_id=<?php echo $chat_id; ?>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao verificar status:', error);
                });
        }, 5000);  // Verifica a cada 5 segundos
      </script>
</body>
</html>
