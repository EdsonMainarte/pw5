<?php
require '../Dao.php';

$dao = new Dao();
$pdo = $dao->getPdo(); // Conexão com o banco de dados

session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('location: ../index.php');
    exit();
}

$user_email = $_SESSION['email'];

if (empty($user_email)) {
    echo "Usuário não autenticado.";
    exit();
}

// Verifica se os IDs foram passados e são numéricos
if (!isset($_GET['id_cartao']) || !is_numeric($_GET['id_cartao']) ||
    !isset($_GET['id_lista']) || !is_numeric($_GET['id_lista']) ||
    !isset($_GET['id_quadro']) || !is_numeric($_GET['id_quadro'])) {
    echo "ID de cartão, lista ou quadro inválido.";
    exit();
}

$cartao_id = (int)$_GET['id_cartao'];
$lista_id = (int)$_GET['id_lista'];
$quadro_id = (int)$_GET['id_quadro'];

// Obtém os detalhes do cartão
$stmt = $pdo->prepare("SELECT * FROM cartoes WHERE id_cartao = :id_cartao");
$stmt->execute(['id_cartao' => $cartao_id]);
$cartao = $stmt->fetch();

if (!$cartao) {
    echo "Cartão não encontrado.";
    exit();
}

// Obtém a lista de membros do quadro
$stmt = $pdo->prepare("
    SELECT u.cod, u.nome 
    FROM users u
    INNER JOIN membros_quadro m ON u.cod = m.user_id
    WHERE m.board_id = :board_id
");
$stmt->execute(['board_id' => $quadro_id]);
$membros = $stmt->fetchAll();

// Obtém os anexos do cartão
$stmt = $pdo->prepare("SELECT * FROM arquivos_cartao WHERE id_cartao = :id_cartao");
$stmt->execute(['id_cartao' => $cartao_id]);
$anexos = $stmt->fetchAll();

// Processa a exclusão de anexos
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $arquivo_id = (int)$_GET['delete'];
    
    // Obtém o caminho do arquivo a partir do banco de dados
    $stmt = $pdo->prepare("SELECT caminho_arquivo FROM arquivos_cartao WHERE id_arquivo = :id_arquivo");
    $stmt->execute(['id_arquivo' => $arquivo_id]);
    $arquivo = $stmt->fetch();

    if ($arquivo) {
        $file_path = $arquivo['caminho_arquivo'];
        
        // Remove o arquivo do sistema de arquivos
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Remove o registro do arquivo do banco de dados
        $stmt = $pdo->prepare("DELETE FROM arquivos_cartao WHERE id_arquivo = :id_arquivo");
        $stmt->execute(['id_arquivo' => $arquivo_id]);
        
        header("Location: edit_cartao.php?id_cartao=$cartao_id&id_lista=$lista_id&id_quadro=$quadro_id");
        exit();
    } else {
        echo "Arquivo não encontrado.";
    }
}

// Atualiza o cartão no banco de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_cartao = $_POST['nome_cartao'];
    $descricao_cartao = $_POST['descricao_cartao'];
    $prazo = $_POST['prazo'];
    $responsavel_id = isset($_POST['responsavel']) && is_numeric($_POST['responsavel']) ? (int)$_POST['responsavel'] : null;

    // Atualiza o cartão
    $stmt = $pdo->prepare("
        UPDATE cartoes 
        SET nome_cartao = :nome_cartao, descricao_cartao = :descricao_cartao, prazo = :prazo, membro_responsavel_id = :responsavel_id
        WHERE id_cartao = :id_cartao
    ");
    $stmt->execute([
        'nome_cartao' => $nome_cartao,
        'descricao_cartao' => $descricao_cartao,
        'prazo' => $prazo,
        'responsavel_id' => $responsavel_id,
        'id_cartao' => $cartao_id
    ]);

    // Processa o upload de novos arquivos
    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['arquivo']['tmp_name'];
        $file_name = basename($_FILES['arquivo']['name']);
        $upload_dir = '../uploads/'; // Diretório para salvar os arquivos
        $file_path = $upload_dir . $file_name;

        // Verificar se o diretório de uploads existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Mover o arquivo para o diretório de uploads
        if (move_uploaded_file($file_tmp_name, $file_path)) {
            // Inserir informações do arquivo no banco de dados
            $stmt = $pdo->prepare("
                INSERT INTO arquivos_cartao (id_cartao, nome_arquivo, caminho_arquivo)
                VALUES (:id_cartao, :nome_arquivo, :caminho_arquivo)
            ");
            $stmt->execute([
                'id_cartao' => $cartao_id,
                'nome_arquivo' => $file_name,
                'caminho_arquivo' => $file_path
            ]);
        } else {
            echo "Erro ao mover o arquivo para o diretório de uploads.";
        }
    }

    header("Location: ../quadro.php?id=$quadro_id");
    exit();
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Cartão - Sicron</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS inline para estilos básicos */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #007bff;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .logo {
            font-size: 1.5em;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin-left: 10px;
        }

        nav a {
            color: white;
            text-decoration: none;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            margin-top: 0;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .form-group input[type="file"] {
            border: none;
        }

        .btn {
            display: inline-block;
            padding-right: 70%;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            font-size: 1em;
            cursor: pointer;
            text-align: center;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .comentarios {
            margin-top: 20px;
        }

        .comentario {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .comentario .autor {
            font-weight: bold;
        }

        .comentario .data {
            color: #777;
        }

        .comentario-resposta {
            margin-left: 20px;
            border-left: 2px solid #ddd;
            padding-left: 10px;
        }

        .comentario-resposta .autor {
            color: #007bff;
        }
    </style>
</head>
<body>
    <nav>
        <label class="logo">Sicron</label>
    </nav>  
    <div class="container">
        <h1>Editar Cartão</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome_cartao">Nome do Cartão:</label>
                <input type="text" id="nome_cartao" name="nome_cartao" value="<?php echo htmlspecialchars($cartao['nome_cartao']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descricao_cartao">Descrição:</label>
                <textarea id="descricao_cartao" name="descricao_cartao" rows="4" required><?php echo htmlspecialchars($cartao['descricao_cartao']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="prazo">Prazo:</label>
                <input type="date" id="prazo" name="prazo" value="<?php echo $cartao['prazo']; ?>" required>
            </div>
            <div class="form-group">
                <label for="responsavel">Responsável:</label>
                <select name="responsavel" id="responsavel">
                    <option value="">Selecione o responsável</option>
                    <?php foreach ($membros as $membro): ?>
                        <option value="<?php echo $membro['cod']; ?>" <?php echo $membro['cod'] == $cartao['membro_responsavel_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($membro['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="arquivo">Novo Anexo:</label>
                <input type="file" id="arquivo" name="arquivo" onchange="previewImage(event)">
            </div>

            <!-- Espaço para exibir a imagem -->
            <div id="imagePreview" style="display: none;">
                <h3>Pré-visualização da Imagem:</h3>
                <img id="previewImage" src="" alt="Pré-visualização da imagem" style="max-width: 100%; height: auto;">
            </div>

            <button type="submit" class="btn">Atualizar Cartão</button>
        </form>

        <div class="comentarios">
            <h3>Anexos:</h3>
            <?php foreach ($anexos as $anexo): ?>
                <div class="comentario">
                    <a href="../<?php echo htmlspecialchars($anexo['caminho_arquivo']); ?>" target="_blank"><?php echo htmlspecialchars($anexo['nome_arquivo']); ?></a> |
                    <a href="edit_cartao.php?id_cartao=<?php echo $cartao_id; ?>&id_lista=<?php echo $lista_id; ?>&id_quadro=<?php echo $quadro_id; ?>&delete=<?php echo $anexo['id_arquivo']; ?>">Excluir</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    function previewImage(event) {
        const preview = document.getElementById('imagePreview');
        const image = document.getElementById('previewImage');
        
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function() {
            image.src = reader.result;
            preview.style.display = 'block'; // Exibe a pré-visualização
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
    </script>
</body>
</html>
