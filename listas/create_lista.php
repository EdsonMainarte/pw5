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

// Verifica se o ID do quadro foi passado
if (!isset($_GET['board_id']) || !is_numeric($_GET['board_id'])) {
    echo "ID de quadro inválido.";
    exit();
}

$quadro_id = (int)$_GET['board_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_lista = $_POST['nome_lista'];
    $stmt = $pdo->prepare("INSERT INTO listas (nome_lista, id_quadro, created_at) VALUES (:nome_lista, :id_quadro, NOW())");
    $stmt->execute(['nome_lista' => $nome_lista, 'id_quadro' => $quadro_id]);

    header("Location: ../quadro.php?id=$quadro_id");
    exit();
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar Lista - Sicron</title>
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

        form .form-group {
            margin-bottom: 15px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
        }

        form input[type="text"], form textarea {
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
        <h1>Criar Nova Lista</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="nome_lista">Nome da Lista:</label>
                <input type="text" id="nome_lista" name="nome_lista" required>
            </div>
            <input type="hidden" name="id_quadro" value="<?php echo htmlspecialchars($quadro_id); ?>">
            <button type="submit">Criar Lista</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2024 Sicron</p>
    </footer>
</body>
</html>
