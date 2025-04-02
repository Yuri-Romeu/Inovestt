<?php
session_start();

include('protect.php'); // Protege a página para usuários autenticados
include('conexao.php'); // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit;
}

// Recupera o ID da empresa via GET (a partir do botão "Saiba Mais")
if (!isset($_GET['id'])) {
    echo "ID da empresa não fornecido!";
    exit;
}

$empresa_id = $_GET['id'];

// Consulta para recuperar os dados da empresa e do empreendedor
$query = "
  SELECT 
    empresas.nome AS nome_empresa, 
    empresas.cnpj, 
    empresas.detalhes, 
    empresas.categoria, 
    empresas.tipo_empresa,
    empresas.website, 
    empresas.foto_perfil,
    empresas.usuario_id AS empreendedor_id, -- Adicionei para recuperar o ID do empreendedor
    empreendedor.nome AS nome_empreendedor 
  FROM empresas 
  JOIN empreendedor 
  ON empresas.usuario_id = empreendedor.id 
  WHERE empresas.id = ?
";

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saiba Mais Empresa</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }

        body {
            font-family: 'poppins', sans-serif;
        }

         /* ===========================menu==============  */

        header {
            background-color: #EAB81B;
            height: 130px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            gap: 20px;
        }


        .voltar {
            background-color: #232333;
            width: 47px;
            height: 47px;
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
           padding: 12px;
           height: 50px;
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

         /* ===========================principal=============  */

        main {
            background-color: #141418;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        main .proposta {
            background-color: #1D1D25;
            width: 1200px;
            height: 93%;
            border-radius: 13px;
            margin-top: -20px;
            padding: 15px;
            padding-left: 30px;
            padding-right: 30px;
            font-family: 'poppins', sans-serif;
        }

        main .proposta .title-proposta {
            text-align: center;
            color: #fff;
            font-size: 18px;
            font-weight: 400;
        }

        main .proposta img {
            float: left;
            margin-right: 10px;
        }

        main .proposta .nome-empresa {
            margin-bottom: 30px;
            margin-top: 5px;
            font-family: 'poppins', sans-serif;
            color: #fff;
            font-weight: 400;
            font-size: 18px;
        }

        main .proposta .detalhes {
            font-family: 'poppins', sans-serif;
            color: #fff;
            font-size: 15px;
            font-weight: 300;
            text-align: start;
            width: 1150px;
            margin-bottom: 20px;
            white-space: pre-wrap; /* Mantém as quebras de linha e ajusta o texto */
            overflow: auto; /* Adiciona barra de rolagem quando necessário */
            max-height: 180px; 
        }

        main .proposta button {
            display: block;
            margin: 0 auto;
            width: 1130px;
            height: 50px;
            background-color: #808080;
            padding: 10px;
            color: #101012;
            font-family: 'poppins', sans-serif;
            font-weight: 500;
            border: none;
            font-size: 18px;
            border-radius: 11px;
            cursor: not-allowed;
            transition: 0.3s;
        }
        
        main .proposta h4 {
            font-family: 'poppins', sans-serif;
            color: #fff;
            font-weight: 400;
            font-size: 17px;
            margin-bottom: 2%;
            display: inline-block;
        }

          /* ===========================fim principal==============  */

    </style>
</head>
<body>
    <header>
        <button style="all: unset;" onclick="window.history.back()"> 
        <div class="voltar">
            <img src="Imagens-princ/setinha-voltar.png" alt="seta voltar" width="16px">
        </div>
        </button>

        <div class="pesquisa">
            <input type="text" placeholder="Procurar Empresa">
            <img src="Imagens-princ/icon-lupa.png" alt="" width="20px">
        </div>

        <div class="icon-perfil">
        <button style="all: unset; cursor: pointer;" onclick="window.history.back()"> 
        <img src="<?php echo !empty($_SESSION['user_data']['foto_perfil']) ? $_SESSION['user_data']['foto_perfil'] : 'Imagens-princ/icon-perfil-menu.png'; ?>" alt="" width="45px" height="45px" style="border-radius: 50%; object-fit: cover;">
        </button>
        </div>
    </header>

    <main>
        <div class="proposta">
            <h2 class="title-proposta">Detalhes da Empresa</h2>
            <img src="<?php echo !empty($empresa['foto_perfil']) ? $empresa['foto_perfil'] : 'Imagens-princ/icon-perfil-menu.png'; ?>" alt="" height="38px" width="38px" style="border-radius: 50%; object-fit: cover;">
            <h3 class="nome-empresa"><?php echo htmlspecialchars($empresa['nome_empresa']); ?></h3>
            <p class="detalhes"><?php echo htmlspecialchars($empresa['detalhes']); ?></p>
                 <h4><strong>Categoria:</strong></h4> <h4><?php echo htmlspecialchars($empresa['categoria']); ?></h4><br>
                 <h4><strong>Tipo da Empresa:</strong></h4> <h4><?php echo htmlspecialchars($empresa['tipo_empresa']); ?></h4><br>
                 <h4><strong>Empreendedor:</strong></h4> <h4><?php echo htmlspecialchars($empresa['nome_empreendedor']); ?></h4><br>
                 <h4 style="margin-bottom: 4%;"><strong>Website:</strong></h4> <h4><?php echo htmlspecialchars($empresa['website']); ?></h4>
                
                 <button class="botao-proposta" onclick="apenasInvestidor()">
                    Fazer Proposta
                </button>

        </div><br><br>
    </main>
   

    <script>
        function apenasInvestidor() {
           alert("Apenas Investidores podem fazer propostas");
        }
    </script>
</body>
</html>