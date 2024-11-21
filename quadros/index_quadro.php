<?php
require '../Dao.php';

$dao = new Dao();
$pdo = $dao->getPdo(); // Conexão com o banco de dados

$quadros = $pdo->query("SELECT * FROM quadros")->fetchAll();
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ver Quadros</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        .container {
            max-width: 800px;
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
        .quadro-list {
            list-style: none;
            padding: 0;
        }
        .quadro-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            padding: 15px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .quadro-color {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #ccc;
            margin-right: 15px;
        }
        .quadro-details {
            flex: 1;
        }
        .quadro-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .quadro-description {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }
        .quadro-actions {
            text-align: right;
        }
        .quadro-actions a {
            margin-left: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .quadro-actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Quadros</h1>
        <ul class="quadro-list">
            <?php foreach ($quadros as $quadro): ?>
                <li class="quadro-item">
                    <div class="quadro-color" style="background: <?php echo htmlspecialchars($quadro['cor_quadro']); ?>"></div>
                    <div class="quadro-details">
                        <p class="quadro-name"><?php echo htmlspecialchars($quadro['nome_quadro']); ?></p>
                        <p class="quadro-description"><?php echo htmlspecialchars($quadro['descricao_quadro']); ?></p>
                    </div>
                    <div class="quadro-actions">
                        <a href="quadros/edit_quadro.php?id=<?php echo $quadro['id_quadro']; ?>">Editar</a>
                        <a href="quadros/delete_quadro.php?id=<?php echo $quadro['id_quadro']; ?>">Excluir</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
