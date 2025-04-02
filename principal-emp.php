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

// Recupera filtros do formulário
$categorias = isset($_GET['categorias']) ? $_GET['categorias'] : [];
$categoria_selecionada = isset($_GET['categoria']) && $_GET['categoria'] !== 'sem_categoria' ? $mysqli->real_escape_string($_GET['categoria']) : '';
$tipo_empresa = isset($_GET['tipo_empresa']) && $_GET['tipo_empresa'] !== 'Sem_dados' ? $mysqli->real_escape_string($_GET['tipo_empresa']) : '';
$nome_empresa = isset($_GET['nome_empresa']) ? $mysqli->real_escape_string($_GET['nome_empresa']) : '';

// Query para recuperar as empresas cadastradas
$sql_empresas = "SELECT * FROM empresas WHERE 1=1";

// Filtro por múltiplas categorias (checkbox)
if (!empty($categorias)) {
    $categorias_list = implode("','", array_map([$mysqli, 'real_escape_string'], $categorias));
    $sql_empresas .= " AND categoria IN ('$categorias_list')";
}

// Filtro por categoria (select)
if (!empty($categoria_selecionada)) {
    $sql_empresas .= " AND categoria = '$categoria_selecionada'";
}

// Filtro por tipo de empresa
if (!empty($tipo_empresa)) {
    $sql_empresas .= " AND tipo_empresa = '$tipo_empresa'";
}

// Filtro por nome de empresa
if (!empty($nome_empresa)) {
    $sql_empresas .= " AND nome LIKE '%$nome_empresa%'";
}

// Executa a query
$result_empresas = $mysqli->query($sql_empresas);

// Array para armazenar os resultados
$empresas = [];

if ($result_empresas->num_rows > 0) {
    while ($row = $result_empresas->fetch_assoc()) {
        $empresas[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Plataforma Inovestt</title>
  <link rel="stylesheet" href="principal-emp.css">
</head>
<body>
  <header id="header">
  <form method="GET" action="principal-emp.php">
      <input type="text" class="pesquisa" name="nome_empresa" placeholder="Procurar Empresa">
   </form>   
      <img src="Imagens-princ/icon-lupa.png" alt="" width="17px" style="position: absolute; right: 13%; z-index: 1000; top: 41%;">
      <div class="icon-profile">
      <a href="#cardMenu"><img id="img-perfil" src="<?php echo !empty($_SESSION['user_data']['foto_perfil']) ? $_SESSION['user_data']['foto_perfil'] : 'Imagens-princ/icon-perfil-menu.png'; ?>" alt="" width="42px" height="42px" style="border-radius: 50%; object-fit: cover;"></a>
      </div>
  </header>

<!--============================menu==de==perfil============================== -->

<div class="card-menu" id="cardMenu">
    <div class="card-menu-div">
        <h1><?php echo htmlspecialchars(ucfirst($tipo_usuario)); ?></h1>
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
            <a href="editar-empresa.php" style="all: unset;">
                <img src="Imagens-princ/outra-conta.png" alt="" width="23px"><h5>Dados da Empresa</h5>
            </a>
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


  <aside>
    <div class="area-add-empresa">
      <a href="principal-emp.php"><img src="Imagens-princ/icon-perfil-menu.png" alt="" width="60px"></a>
      <div class="text-add-empresa">
        <p>Nova Empresa</p>
        <button><a href="adicionar-empresa.php" style="all: unset;">Adicionar</a></button>
      </div>
    </div>
    <hr>
    <div class="filtro">
      <p>Filtro</p>

      <form method="GET" action="principal-emp.php">
        <select name="categoria" id="categoria">
                <option value="sem_categoria">Categoria</option>
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
        </select>

        <select name="tipo_empresa" id="tipo_empresa">
            <option value="Sem_dados">Tipo de Empresa</option>
            <option value="Empresa_Individual">Empresa Individual</option>
            <option value="MEI">Microempreendedor Individual (MEI)</option>
            <option value="Ltda">Sociedade Empresária Limitada (Ltda)</option>
            <option value="Sociedade_limitada_unipessoal">Sociedade Limitada Unipessoal</option>
            <option value="SS">Sociedade Simples (SS)</option>
            <option value="S/A">Sociedade Anônima (S/A)</option>
            <option value="EPP">Empresa de pequeno porte (EPP)</option>
            <option value="Empresa_medio_grande">Empresas de médio e grande porte</option>
        </select>

      <p class="text-categoria">Categoria</p>

      <div class="checkbox-cat">
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado1" name="categorias[]" value="Alimentacao_Bebidas">
            <label for="quadrado1">Aliment. e Bebidas</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado2" name="categorias[]" value="Comunicacao_Midia">
            <label for="quadrado2">Comunic. e Mídia</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado3" name="categorias[]" value="Construcao_Civil">
            <label for="quadrado3">Construção Civil</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado4" name="categorias[]" value="Financas">
            <label for="quadrado4">Finanças</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado5" name="categorias[]" value="Turismo_Hotelaria">
            <label for="quadrado5">Turismo e Hotelaria</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado6" name="categorias[]" value="Transporte_Logistica">
            <label for="quadrado6">Transp. e Logística</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado7" name="categorias[]" value="Varejo">
            <label for="quadrado7">Varejo</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado8" name="categorias[]" value="Industria">
            <label for="quadrado8">Indústria</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado9" name="categorias[]" value="Educacao">
            <label for="quadrado9">Educação</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado10" name="categorias[]" value="Energia">
            <label for="quadrado10">Energia</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado11" name="categorias[]" value="Saude">
            <label for="quadrado11">Saúde</label>
          </div>
          <div class="checkbox-row">
            <input type="checkbox" id="quadrado12" name="categorias[]" value="Tecnologia_Informacao">
            <label for="quadrado12">TI</label>
          </div>
        </div><br>

        <div class="btn-filter">
            <button type="submit" class="apl-fil">Aplicar Filtro</button>
        </div>
      </form>
    </div>
  </aside>

<main style="color: #fff;">
    <br><br><br><a href="chat-ativo-emp.php" style="all: unset;"><div class="chat-ativo"><img src="imagens-princ/chat-ativo.png" alt=""></div></a>

    <!-- ============================propostas================ -->

    <!-- Empresas filtradas -->
    <?php if (!empty($empresas)): ?>
        <?php foreach ($empresas as $empresa): ?>
            <div class="proposta">
                <div class="img-proposta">
                <img src="<?php echo !empty($empresa['foto_perfil']) ? $empresa['foto_perfil'] : 'Imagens-princ/icon-perfil-menu.png'; ?>" alt="" height="86px" width="86px" style="border-radius: 50%; object-fit: cover;">
                </div>
                <div class="text-proposta">
                    <h1><?php echo htmlspecialchars($empresa['nome']); ?></h1>
                    <p class="detalhes"><?php echo htmlspecialchars($empresa['detalhes']); ?></p>
                </div>
                <div class="area-butoes-proposta">
                    <button id="button-proposta" onclick="button_proposta()">Proposta</button>
                    <button id="saibamais-proposta"><a href="detalhes-empresa-emp.php?id=<?php echo $empresa['id']; ?>" style="all: unset;">Saiba Mais</a></button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nenhuma empresa cadastrada.</p>
    <?php endif; ?>

     <!-- ============================fim=propostas================ -->
  </main>


  <script>

        function button_proposta()
            {
            alert("Apenas Investidores podem fazer propostas");
            }

            // =====================card menu=usuarios===========================

            document.getElementById('toggle-companies').addEventListener('click', function() {
                var companyList = document.getElementById('company-list');
                var cardMenu = document.getElementById('cardMenu');
                if (companyList.classList.contains('hidden')) {
                    companyList.classList.remove('hidden');
                    cardMenu.classList.add('expanded');
                } else {
                    companyList.classList.add('hidden');
                    cardMenu.classList.remove('expanded');
                }
            });

// ==================fim===card menu=usuario===========================


            // =====================card menu============================

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



// ====================fim=card menu============================


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

        </script>
</body>
</html>
