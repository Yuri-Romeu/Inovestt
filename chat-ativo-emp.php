<?php
session_start();
include('conexao.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Plataforma Inovestt</title>
  <link rel="stylesheet" href="principal-inv.css">
  <style>
    .iframe-container {
            display: none; /* Começa oculto */
            position: fixed; /* Fixa o iframe na tela */
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%); /* Centraliza o iframe */
            z-index: 99999; /* Garante que o iframe apareça sobre outros elementos */
            width: 80%; /* Largura do iframe */
            height: 80%; /* Altura do iframe */
          }

          .iframe-container iframe {
            width: 100%; /* O iframe ocupa toda a largura do container */
            height: 100%; /* O iframe ocupa toda a altura do container */
            border: none; /* Remove a borda do iframe */
          }

          .close-btn {
            position: absolute;
            top: 145px;
            right: 210px;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 20px;
            background: transparent;
            z-index: 1000; /* Garante que o botão de fechar apareça sobre o iframe */
          }

  </style>
</head>
<body>
  <header id="header">
  <a href="principal-emp.php" style="all: unset; padding: 0; margin: 0;"><img src="Imagens/icon-ino-black.png" alt="Icon Inovestt" style="padding: 0; margin: 0;" class="logo-img"></a>
  <form method="GET" action="principal-emp.php">
      <input type="text" class="pesquisa" name="nome_empresa" placeholder="Procurar Empresa">
   </form> 
      <img src="Imagens-princ/icon-lupa.png" alt="" width="17px" style="position: absolute; right: 10%; z-index: 1000; top: 41%;">
      <div class="icon-profile">
      <a href="#cardMenu" style="all: unset;"><img id="img-perfil" src="<?php echo !empty($_SESSION['user_data']['foto_perfil']) ? $_SESSION['user_data']['foto_perfil'] : 'Imagens-princ/icon-perfil-menu.png'; ?>" alt="" width="42px" height="42px" style="border-radius: 50%; object-fit: cover;"></a>
      </div>
  </header>


<!--============================menu==de==perfil============================== -->

<div class="card-menu" id="cardMenu">
    <div class="card-menu-div">
        <h1>Empreendedor</h1>
        <img src="<?php echo !empty($_SESSION['user_data']['foto_perfil']) ? $_SESSION['user_data']['foto_perfil'] : 'Imagens-princ/icon-perfil-menu.png'; ?>" alt="" width="70px" height="70px" style="border-radius: 50%; object-fit: cover;">
        <h2><?php echo htmlspecialchars($_SESSION['user_data']['nome_perfil']); ?></h2><br>
        <h3><?php echo htmlspecialchars($_SESSION['user_data']['email']); ?></h3>

        <button><a href="adicionar-empresa.php" style="all: unset;">Adicionar Empresa</a></button>

        <button id="toggle-companies">Mudar de Empresa</button>

        <div id="company-list" class="hidden">
            <div class="company">
                <img src="Imagens-princ/icon-perfil-menu.png" alt="Avatar" width="18px">
                <p>Username</p>
            </div>
            <hr>
            <div class="company">
                <img src="Imagens-princ/icon-perfil-menu.png" alt="Avatar" width="18px">
                <p>Username</p>
            </div>
        </div>

        <div class="configs">
            <button class="aba-config">
                <a href="editar-perfil.php" style="all: unset;">
                <img src="Imagens-princ/icon-config.png" alt="" width="20px"><h5>Configurações da conta</h5>
                </a>
            </button>

            <button class="aba-config">
                <img src="Imagens-princ/outra-conta.png" alt="" width="23px"><h5>Dados da Empresa</h5>
            </button>

            <button class="aba-config">
                <img src="Imagens-princ/user.png" alt="" width="17px"><h5>Mudar de conta</h5>
            </button>

            <button class="aba-config">
                <img src="Imagens-princ/exit.png" alt="" width="18px"><h5><a href="logout.php" style="all: unset;">Desconectar</a></h5>
            </button>
        </div>
    </div>
</div>


<!--=========================fim===menu==de==perfil============================== -->



  <main style="color: #fff; margin-left: 40px;"><center>
    <!-- Conteúdo principal -->
    <br><br><br><a href="principal-emp.php" style="all: unset;"><div style="position: absolute; top: 87.8px; left: 460px; cursor: pointer;"><img src="imagens-princ/chat-ativo.png" alt=""></div></a>




    <?php
      // Verifica se o usuário está logado
      if (!isset($_SESSION['id'])) {
        echo "Você precisa estar logado para acessar os chats.";
        exit;
      }

      $usuario_id = $_SESSION['id'];
      $tipo_usuario = $_SESSION['tipo']; // 'investidor' ou 'empreendedor'

      // Seleciona os chats relacionados ao usuário logado
      if ($tipo_usuario == 'investidor') {
        $query_chats = "
          SELECT c.id, e.nome AS nome_empreendedor, e.foto_perfil, e.bibliografia, c.status_negociacao
          FROM chats c
          JOIN empreendedor e ON c.id_empreendedor = e.id
          WHERE c.id_investidor = ?
        ";
      } else {
        $query_chats = "
          SELECT c.id, i.nome AS nome_investidor, i.foto_perfil, i.bibliografia, c.status_negociacao
          FROM chats c
          JOIN investidor i ON c.id_investidor = i.id
          WHERE c.id_empreendedor = ?
        ";
      }

      $stmt_chats = $mysqli->prepare($query_chats);
      $stmt_chats->bind_param("i", $usuario_id);
      $stmt_chats->execute();
      $result_chats = $stmt_chats->get_result();

      // Verifica se o usuário possui chats
      if ($result_chats->num_rows > 0) {
        echo "<h2>Seus Chats</h2>";

        // Exibe todos os chats
        while ($chat = $result_chats->fetch_assoc()) {
          echo "<div class='card-chat'>";
          echo "<div class='card-chat-header'>";

          $foto_perfil = !empty($chat['foto_perfil']) ? $chat['foto_perfil'] : 'Imagens-princ/icon-perfil-menu.png';
          echo "<img src='" . htmlspecialchars($foto_perfil) . "' alt='Avatar' class='avatar' height='86px' width='86px' style='border-radius: 50%; object-fit: cover;'>";

          echo "<div class='company-info'>";

          if ($tipo_usuario == 'investidor') {
            echo "<h2 class='company-name'>Chat com " . htmlspecialchars($chat['nome_empreendedor']) . "</h2>";
            echo "<p class='company-description'>" . htmlspecialchars($chat['bibliografia']) . "</p>";
          } else {
            echo "<h2 class='company-name'>Chat com " . htmlspecialchars($chat['nome_investidor']) . "</h2>";
            echo "<p class='company-description'>" . htmlspecialchars($chat['bibliografia']) . "</p>";
          }

          echo "</div>"; // Fecha company-info
          echo "</div>"; // Fecha card-chat-header

          echo "<div class='card-footer'>";

          // Verifica o status da negociação
          if (isset($chat['status_negociacao']) && $chat['status_negociacao'] == 'concluido') {
            echo "<p>Negócio Concluído</p>";
          } else {
            echo "<button class='closed-deal open-iframe-btn' data-chat-id='" . $chat['id'] . "'>Negócio Fechado</button>";
            echo "<a href='chat.php?chat_id=" . $chat['id'] . "' class='open-chat' style='text-decoration: none; padding-top: 5px;'>Abrir Chat</a>";
          }

          echo "</div>"; // Fecha card-footer
          echo "</div>"; // Fecha card-chat
          echo "<br>";
        }
      } else {
        echo "Você ainda não possui chats ativos.";
      }
      ?>

        <!-- Container do iframe fora da div -->
        <div class="iframe-container">
            <button class="close-btn"><img src="imagens-princ/X.png" alt="" width="18px"></button>
            <iframe src="" frameborder="0"></iframe>
        </div>


    </main>


<script>

          // Obtém os elementos da imagem e da div
      const imgPerfil = document.getElementById('img-perfil');
      const cardMenu = document.getElementById('cardMenu');

      // Função para mostrar a div
      function showCardMenu() {
          cardMenu.style.display = 'block';
      }

      // Função para ocultar a div
      function hideCardMenu() {
          cardMenu.style.display = 'none';
      }

      // Adiciona o evento de mouseover na imagem
      imgPerfil.addEventListener('mouseover', showCardMenu);

      // Adiciona o evento de mouseout na imagem
      imgPerfil.addEventListener('mouseout', function() {
          setTimeout(function() {
              if (!cardMenu.matches(':hover')) {
                  hideCardMenu();
              }
          }, 130); // Pequeno delay para permitir a transição do mouse para a div
      });

      // Adiciona o evento de mouseover na div para mantê-la visível
      cardMenu.addEventListener('mouseover', showCardMenu);

      // Adiciona o evento de mouseout na div para ocultá-la quando o mouse sair
      cardMenu.addEventListener('mouseout', hideCardMenu);



// =====================paragrafo limitado============================

function limitarTexto(elemento, limite) {
          let texto = elemento.innerText;
          if (texto.length > limite) {
              let textoCortado = texto.substring(0, limite) + '. . .';
              elemento.innerText = textoCortado;
          }
      }

      // Seleciona todos os elementos com a classe 'detalhes' e aplica o limite de 300 caracteres
      const detalhesList = document.querySelectorAll('.detalhes');
      detalhesList.forEach(function(detalhes) {
          limitarTexto(detalhes, 300);
      });



  // =====================fim=paragrafo=limitado==========================


  function iniciarChat(empreendedorId) {
  // Redireciona para a página de chat com o empreendedor
  window.location.href = "chat.php?empreendedor_id=" + empreendedorId;
}


  // =====================iframe==========================
// Manipulação do botão para abrir o iframe
document.querySelectorAll('.open-iframe-btn').forEach(button => {
    button.addEventListener('click', function() {
        const chatId = this.dataset.chatId;
        const iframeContainer = document.querySelector('.iframe-container');
        const iframe = iframeContainer.querySelector('iframe');

        // Define a URL correta do iframe
        iframe.src = `concluir-negocio.php?chat_id=${chatId}`;

        // Exibe o iframe
        iframeContainer.style.display = 'block';
    });
});

// Manipulação do botão de fechar o iframe
document.querySelector('.close-btn').addEventListener('click', function() {
    const iframeContainer = document.querySelector('.iframe-container');
    iframeContainer.style.display = 'none'; // Oculta o iframe
});


// FIM PARTE IFRAME


      </script>
</body>
</html>
