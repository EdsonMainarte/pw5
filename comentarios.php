<?php
require 'Dao.php';

$dao = new Dao();
$pdo = $dao->getPdo(); // Conexão com o banco de dados

session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('location: index.php');
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

// Processa a adição de novos comentários
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    $comentario_texto = $_POST['comentario'];
    $comentario_pai_id = isset($_POST['comentario_pai_id']) && is_numeric($_POST['comentario_pai_id']) ? (int)$_POST['comentario_pai_id'] : null;

    // Verifica se o texto do comentário não está vazio
    if (empty($comentario_texto)) {
        echo "O comentário não pode estar vazio.";
        exit();
    }

    // Verifica se o comentario_pai_id existe, se não for nulo
    if ($comentario_pai_id !== null) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM comentarios WHERE id_comentario = :comentario_pai_id");
        $stmt->execute(['comentario_pai_id' => $comentario_pai_id]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            echo "Comentário pai não encontrado.";
            exit();
        }
    }

    // Insere o novo comentário
    $stmt = $pdo->prepare("
        INSERT INTO comentarios (id_cartao, user_email, texto, comentario_pai_id)
        VALUES (:id_cartao, :user_email, :texto, :comentario_pai_id)
    ");
    $stmt->execute([
        'id_cartao' => $cartao_id,
        'user_email' => $user_email,
        'texto' => $comentario_texto,
        'comentario_pai_id' => $comentario_pai_id
    ]);

    header("Location: comentarios.php?id_cartao=$cartao_id&id_lista=$lista_id&id_quadro=$quadro_id");
    exit();
}

// Obtém os comentários do cartão
$stmt = $pdo->prepare("SELECT c.*, u.nome as usuario_nome FROM comentarios c
    INNER JOIN users u ON c.user_email = u.email
    WHERE c.id_cartao = :id_cartao
    ORDER BY c.data_hora DESC"); // Ordenação do mais recente para o mais antigo
$stmt->execute(['id_cartao' => $cartao_id]);
$comentarios = $stmt->fetchAll();
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comentários Cartão - Sicron</title>
    <link rel="stylesheet" href="style.css">
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
            width: 150px; /* Define a largura específica do botão */
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            font-size: 0.9em;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            box-sizing: border-box;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .form-group .btn {
            width: auto; /* Garante que o botão tenha largura automática no formulário */
            margin-top: 10px;
            display: block;
        }

        .comentarios {
            margin-top: 20px;
        }

        .comentario {
            border-bottom: 1px solid #ddd;
            padding: 15px;
            position: relative;
            margin-bottom: 15px; /* Espaçamento entre os comentários */
        }

        .comentario .autor {
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .comentario .data {
            color: #777;
            font-size: 0.9em;
        }

        .comentario .texto {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 10px;
            color: #333;
            line-height: 1.5;
        }

        .comentario-actions {
            display: flex;
            justify-content: flex-end; /* Alinha os botões à direita */
            gap: 10px; /* Espaçamento entre os botões */
        }

        .comentario-actions button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 0.8em;
            cursor: pointer;
            width: 80px; /* Largura fixa dos botões */
        }

        .comentario-actions button:hover {
            background-color: #0056b3;
        }

        .comentario-actions .btn-edit {
            background-color: #28a745;
        }

        .comentario-actions .btn-edit:hover {
            background-color: #218838;
        }

        .comentario-actions .btn-delete {
            background-color: #dc3545;
        }

        .comentario-actions .btn-delete:hover {
            background-color: #c82333;
        }

        .comentario-resposta {
            margin-left: 20px;
            border-left: 2px solid #ddd;
            padding-left: 10px;
            padding-top: 10px;
        }

        .comentario-resposta .autor {
            color: #007bff;
        }

        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <nav>
        <label class="logo">Sicron</label>
    </nav>
    <div class="container">
        <h1>Comentários do cartão</h1>
        <form method="POST">
            <div class="form-group">
                <label for="comentario">Adicionar um comentário:</label>
                <textarea id="comentario" name="comentario" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <input type="hidden" name="comentario_pai_id" value="<?php echo isset($_GET['comentario_pai_id']) ? htmlspecialchars($_GET['comentario_pai_id']) : ''; ?>"> <!-- ID do comentário pai -->
                <button type="submit" class="btn">Enviar Comentário</button>
            </div>
        </form>

        <div class="comentarios">
            <h2>Comentários</h2>
            <?php if (empty($comentarios)): ?>
                <p>Não há comentários nesse cartão.</p>
            <?php else: ?>
                <?php foreach ($comentarios as $comentario): ?>
                    <div class="comentario">
                        <div class="autor">
                            <?php echo htmlspecialchars($comentario['usuario_nome']); ?> 
                            <span class="data"><?php echo htmlspecialchars($comentario['data_hora']); ?></span>
                        </div>
                        <div class="texto"><?php echo nl2br(htmlspecialchars($comentario['texto'])); ?></div>
                        
                        <!-- Verifica se o usuário atual é o autor do comentário -->
                        <?php if ($comentario['user_email'] === $user_email): ?>
                            <div class="comentario-actions">
                                <a href="editar_comentario.php?id_comentario=<?php echo $comentario['id_comentario']; ?>&id_cartao=<?php echo $cartao_id; ?>&id_lista=<?php echo $lista_id; ?>&id_quadro=<?php echo $quadro_id; ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="excluir_comentario.php?id_comentario=<?php echo $comentario['id_comentario']; ?>&id_cartao=<?php echo $cartao_id; ?>&id_lista=<?php echo $lista_id; ?>&id_quadro=<?php echo $quadro_id; ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este comentário?');">
                                    <i class="fas fa-trash"></i> Excluir
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Se houver comentários pai, exibe respostas -->
                        <?php
                        $respostas_stmt = $pdo->prepare("SELECT c.*, u.nome as usuario_nome FROM comentarios c
                            INNER JOIN users u ON c.user_email = u.email
                            WHERE c.comentario_pai_id = :comentario_pai_id
                            ORDER BY c.data_hora");
                        $respostas_stmt->execute(['comentario_pai_id' => $comentario['id_comentario']]);
                        $respostas = $respostas_stmt->fetchAll();
                        ?>
                        <?php foreach ($respostas as $resposta): ?>
                            <div class="comentario-resposta">
                                <div class="autor"><?php echo htmlspecialchars($resposta['usuario_nome']); ?></div>
                                <div class="data"><?php echo htmlspecialchars($resposta['data_hora']); ?></div>
                                <div class="texto"><?php echo nl2br(htmlspecialchars($resposta['texto'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Sicron</p>
    </footer>
</body>
</html>

