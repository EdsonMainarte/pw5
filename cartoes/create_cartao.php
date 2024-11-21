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

// Verifica se o ID da lista foi passado
if (!isset($_GET['id_lista']) || !is_numeric($_GET['id_lista'])) {
    echo "ID de lista inválido.";
    exit();
}

$id_lista = (int)$_GET['id_lista'];

// Obter o ID do quadro associado à lista
$stmt = $pdo->prepare("SELECT id_quadro FROM listas WHERE id_lista = :id_lista");
$stmt->execute(['id_lista' => $id_lista]);
$lista = $stmt->fetch();

if (!$lista) {
    echo "Lista não encontrada.";
    exit();
}

$id_quadro = $lista['id_quadro'];

// Obter a lista de membros do quadro
$stmt = $pdo->prepare("
    SELECT u.cod, u.nome 
    FROM users u
    INNER JOIN membros_quadro m ON u.cod = m.user_id
    WHERE m.board_id = :board_id
");
$stmt->execute(['board_id' => $id_quadro]);
$membros = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome_cartao = $_POST['nome_cartao'];
        $descricao_cartao = $_POST['descricao_cartao'];
        $prazo = $_POST['prazo'];
        $responsavel_id = isset($_POST['responsavel']) && is_numeric($_POST['responsavel']) ? (int)$_POST['responsavel'] : null;

        // Inserir o cartão no banco de dados
        $stmt = $pdo->prepare("
            INSERT INTO cartoes (nome_cartao, descricao_cartao, prazo, id_lista, membro_responsavel_id, created_at)
            VALUES (:nome_cartao, :descricao_cartao, :prazo, :id_lista, :responsavel_id, NOW())
        ");
        $stmt->execute([
            'nome_cartao' => $nome_cartao,
            'descricao_cartao' => $descricao_cartao,
            'prazo' => $prazo,
            'id_lista' => $id_lista,
            'responsavel_id' => $responsavel_id
        ]);

        $id_cartao = $pdo->lastInsertId(); // ID do cartão criado

        // Processar o upload de arquivos
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
                    'id_cartao' => $id_cartao,
                    'nome_arquivo' => $file_name,
                    'caminho_arquivo' => $file_path
                ]);
            } else {
                $_SESSION['mensagem'] = "Erro ao mover o arquivo para o diretório de uploads.";
                header("Location: ../quadro.php?id=$id_quadro");
                exit();
            }
        }

        // Define a mensagem de sucesso e redireciona
        $_SESSION['mensagem'] = "Cartão criado com sucesso!";
        header("Location: ../quadro.php?id=$id_quadro");
        exit();

    } catch (Exception $e) {
        // Define a mensagem de erro e redireciona
        $_SESSION['mensagem'] = "Erro ao criar cartão: " . $e->getMessage();
        header("Location: ../quadro.php?id=$id_quadro");
        exit();
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar Cartão - Sicron</title>
    <link rel="stylesheet" href="../style.css">
    <style>
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

        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form .form-group {
            margin-bottom: 15px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
        }

        form input[type="text"], form textarea, form input[type="date"], form select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
        }

        form button:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <nav>
        <label class="logo">Sicron</label>
    </nav>
    <div class="container">
        <h1>Criar Novo Cartão</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome_cartao">Nome do Cartão:</label>
                <input type="text" id="nome_cartao" name="nome_cartao" required>
            </div>
            <div class="form-group">
                <label for="descricao_cartao">Descrição:</label>
                <textarea id="descricao_cartao" name="descricao_cartao" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="prazo">Prazo:</label>
                <input type="date" id="prazo" name="prazo">
            </div>
            <div class="form-group">
                <label for="arquivo">Anexo:</label>
                <input type="file" id="arquivo" name="arquivo">
            </div>
            <div class="form-group">
                <label for="responsavel">Responsável:</label>
                <select id="responsavel" name="responsavel">
                    <option value="">Nenhum</option>
                    <?php foreach ($membros as $membro): ?>
                        <option value="<?php echo htmlspecialchars($membro['cod']); ?>">
                            <?php echo htmlspecialchars($membro['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="id_lista" value="<?php echo htmlspecialchars($id_lista); ?>">
            <button type="submit">Criar Cartão</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2024 Sicron</p>
    </footer>
</body>
</html>
