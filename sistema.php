<?php
require 'Dao.php';

$dao = new Dao();
$pdo = $dao->getPdo(); // Conexão com o banco de dados

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('location: index.php');
    exit();
}

$logado = $_SESSION['email'];

// Obter o user_id do usuário logado
$stmt = $pdo->prepare("SELECT cod FROM users WHERE email = :email");
$stmt->execute(['email' => $logado]);

// Verifica se a consulta retornou um resultado
$user = $stmt->fetch();
if ($user) {
    $user_id = $user['cod'];
} else {
    // Se não encontrou o usuário, redireciona para login
    header('location: index.php');
    exit();
}

// Obter todos os quadros do usuário
$quadros = $pdo->prepare("SELECT q.* 
    FROM quadros q 
    LEFT JOIN membros_quadro mq ON q.id_quadro = mq.board_id 
    WHERE q.user_id = :user_id OR mq.user_id = :user_id");
$quadros->execute(['user_id' => $user_id]);
$quadros = $quadros->fetchAll();
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sicron</title>
    <link rel="icon" href="logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset e estilo global */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Formulário centralizado */
        .form-container {
            width: 80%;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .form-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .form-container h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .board-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .board-card {
            width: 200px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s;
            text-align: center;
            border: 3px solid #ccc; /* Borda padrão */
        }

        .board-card:hover {
            transform: scale(1.05);
        }

        .board-image {
            height: 150px;
            background-size: cover;
            background-position: center;
            border-radius: 12px 12px 0 0;
        }

        .board-name {
            padding: 10px;
            font-size: 16px;
            color: #333;
        }

        .color-picker {
            margin: 20px;
        }

        /* Estilo para o botão de criar novo quadro */
        .create-board-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0079bf;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .create-board-btn:hover {
            background-color: #0065a1;
        }

        .no-boards {
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>Bem-vindo, <?php echo htmlspecialchars($logado); ?></h1>

    <h2>Seus Quadros</h2>


    <div class="board-list">
        <?php if (count($quadros) > 0): ?>
            <?php foreach ($quadros as $quadro): ?>
                <div class="board-card" id="board-<?php echo $quadro['id_quadro']; ?>" onclick="window.location.href='quadro.php?id=<?php echo $quadro['id_quadro']; ?>'">
                    <!-- Usando a foto do quadro -->
                    <div class="board-image" style="background-image: url('<?php echo "uploads/" . htmlspecialchars($quadro['foto_quadro']); ?>');"></div>

                    <div class="board-name">
                        <strong><?php echo htmlspecialchars($quadro['nome_quadro']); ?></strong>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-boards">Você ainda não criou nenhum quadro.</div>
        <?php endif; ?>
    </div>

    <a href="quadros/create_quadro.php" class="create-board-btn">Criar Novo Quadro</a>
</div>

<script>

</script>

</body>
</html>
