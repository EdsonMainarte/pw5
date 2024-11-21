<?php
require 'Dao.php';

$dao = new Dao();
$pdo = $dao->getPdo();

session_start();
$user_email = $_SESSION['email'];

if (!isset($_GET['id_comentario']) || !is_numeric($_GET['id_comentario'])) {
    echo "ID de comentário inválido.";
    exit();
}

// Captura os parâmetros da URL
$id_comentario = isset($_GET['id_comentario']) ? (int)$_GET['id_comentario'] : null;
$id_cartao = isset($_GET['id_cartao']) ? (int)$_GET['id_cartao'] : null;
$id_lista = isset($_GET['id_lista']) ? (int)$_GET['id_lista'] : null;
$id_quadro = isset($_GET['id_quadro']) ? (int)$_GET['id_quadro'] : null;

if ($id_comentario === null || $id_cartao === null || $id_lista === null || $id_quadro === null) {
    echo "Parâmetros inválidos.";
    exit();
}

$comentario_id = (int)$_GET['id_comentario'];

// Obtém o comentário para edição
$stmt = $pdo->prepare("SELECT * FROM comentarios WHERE id_comentario = :id_comentario");
$stmt->execute(['id_comentario' => $comentario_id]);
$comentario = $stmt->fetch();

if (!$comentario) {
    echo "Comentário não encontrado.";
    exit();
}

// Verifica se o usuário atual é o autor do comentário
if ($comentario['user_email'] !== $user_email) {
    echo "Você não tem permissão para editar este comentário.";
    exit();
}

// Processa a atualização do comentário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['texto'])) {
    $novo_texto = $_POST['texto'];

    if (empty($novo_texto)) {
        echo "O comentário não pode estar vazio.";
        exit();
    }

    $stmt = $pdo->prepare("UPDATE comentarios SET texto = :texto WHERE id_comentario = :id_comentario");
    $stmt->execute([
        'texto' => $novo_texto,
        'id_comentario' => $comentario_id
    ]);

    header("Location: comentarios.php?id_cartao=$id_cartao&id_lista=$id_lista&id_quadro=$id_quadro");
    exit();
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Comentário</title>
    <link rel="stylesheet" href="style.css">
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
        <h1>Editar Comentário</h1>
        <form method="POST">
            <div class="form-group">
                <label for="texto">Texto do Comentário:</label>
                <textarea id="texto" name="texto" rows="6" required><?php echo htmlspecialchars($comentario['texto']); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Salvar</button>
            </div>
        </form>
    </div>
    <footer>
        <p>&copy; 2024 Sicron</p>
    </footer>
</body>
</html>
