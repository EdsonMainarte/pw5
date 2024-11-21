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

$logado = $_SESSION['email'];

// Obter notificações do usuário
// $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_email = :email ORDER BY created_at DESC");
// $stmt->execute(['email' => $logado]);
// $notificacoes = $stmt->fetchAll();
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notificações</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        nav {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        nav .logo {
            font-size: 1.5em;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            display: flex;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            margin-left: 10px;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
        }

        nav a:hover {
            background-color: #0056b3;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            margin-bottom: 20px;
            font-size: 2em;
            text-align: center;
            color: #007bff;
        }

        .container ul {
            list-style: none;
            padding: 0;
        }

        .container ul li {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
        }

        .container ul li:last-child {
            border-bottom: none;
        }

        .container ul li strong {
            display: block;
            font-size: 1.2em;
            color: #007bff;
        }

        .container ul li p {
            margin: 10px 0;
        }

        .container ul li small {
            display: block;
            color: #888;
            font-size: 0.9em;
        }

        .footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px 20px;
            width: 100%;
        }

        /* Media Queries para Responsividade */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: center;
            }

            nav ul {
                flex-direction: column;
                align-items: center;
                width: 100%;
                margin-top: 10px;
                padding: 0;
            }

            nav ul li {
                margin: 5px 0;
            }

            .container {
                margin: 10px;
                padding: 15px;
                box-shadow: none;
            }
        }

        @media (max-width: 480px) {
            nav .logo {
                font-size: 1.2em;
            }

            .container h1 {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">Sicron</div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="notificacoes.php">Notificações</a></li>
            <li><a href="configuracoes.php">Configurações</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Notificações</h1>
        <ul>
            <?php if (count($notificacoes) > 0): ?>
                <?php foreach ($notificacoes as $notificacao): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($notificacao['titulo']); ?></strong>
                        <p><?php echo htmlspecialchars($notificacao['mensagem']); ?></p>
                        <small><?php echo htmlspecialchars($notificacao['created_at']); ?></small>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Não há notificações no momento.</li>
            <?php endif; ?>
        </ul>
    </div>
    <footer class="footer">
        <p>&copy; 2024 Sicron</p>
    </footer>
</body>
</html>
