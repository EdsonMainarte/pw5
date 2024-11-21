<?php
require '../Dao.php';

$dao = new Dao();
$pdo = $dao->getPdo(); // Conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_quadro = $_POST['id_quadro'];
    $nome_quadro = $_POST['nome_quadro'];
    $descricao_quadro = $_POST['descricao_quadro'];
    $cor_quadro = $_POST['cor_quadro'];

    $stmt = $pdo->prepare("UPDATE quadros SET nome_quadro = :nome_quadro, descricao_quadro = :descricao_quadro, cor_quadro = :cor_quadro WHERE id_quadro = :id_quadro");
    $stmt->execute([
        'nome_quadro' => $nome_quadro,
        'descricao_quadro' => $descricao_quadro,
        'cor_quadro' => $cor_quadro,
        'id_quadro' => $id_quadro
    ]);

    header("Location: ../sistema.php"); // Redireciona após a atualização
    exit();
} else {
    $id_quadro = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM quadros WHERE id_quadro = :id_quadro");
    $stmt->execute(['id_quadro' => $id_quadro]);
    $quadro = $stmt->fetch();
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Quadro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        form input[type="text"],
        form input[type="color"],
        form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        form button {
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background: #0056b3;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .color-preview {
            width: 50px;
            height: 50px;
            border-radius: 4px;
            border: 1px solid #ddd;
            display: inline-block;
            margin-top: 5px;
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
        <ul>
            <li><a href="../sistema.php">Voltar</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Editar Quadro</h1>
        <form method="post" action="edit_quadro.php">
            <input type="hidden" name="id_quadro" value="<?php echo htmlspecialchars($quadro['id_quadro']); ?>">

            <div class="form-group">
                <label for="nome_quadro">Nome do Quadro</label>
                <input type="text" id="nome_quadro" name="nome_quadro" value="<?php echo htmlspecialchars($quadro['nome_quadro']); ?>" required>
            </div>

            <div class="form-group">
                <label for="descricao_quadro">Descrição</label>
                <textarea id="descricao_quadro" name="descricao_quadro" rows="4"><?php echo htmlspecialchars($quadro['descricao_quadro']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="cor_quadro">Cor do Quadro</label>
                <input type="color" id="cor_quadro" name="cor_quadro" value="<?php echo htmlspecialchars($quadro['cor_quadro']); ?>" onchange="updateColorPreview(this.value)">
                <div class="color-preview" id="colorPreview" style="background: <?php echo htmlspecialchars($quadro['cor_quadro']); ?>"></div>
            </div>

            <button type="submit">Atualizar Quadro</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2024 Sicron</p>
    </footer>

    <script>
        function updateColorPreview(color) {
            document.getElementById('colorPreview').style.backgroundColor = color;
        }

        // Inicializar a visualização da cor ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            updateColorPreview(document.getElementById('cor_quadro').value);
        });
    </script>
</body>
</html>
