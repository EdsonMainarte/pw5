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

// Obter configurações do usuário
// $stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_email = :email");
// $stmt->execute(['email' => $logado]);
// $configuracoes = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualizar configurações
    $tema = $_POST['tema'];
    $notificacoes = isset($_POST['notificacoes']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE user_settings SET tema = :tema, notificacoes = :notificacoes WHERE user_email = :email");
    $stmt->execute(['tema' => $tema, 'notificacoes' => $notificacoes, 'email' => $logado]);

    // Redirecionar ou mostrar uma mensagem de sucesso
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configurações</title>
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
            max-width: 600px;
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

        .container form {
            display: flex;
            flex-direction: column;
        }

        .container label {
            margin-bottom: 8px;
            font-weight: bold;
        }

        .container select,
        .container input[type="checkbox"] {
            margin-bottom: 15px;
        }

        .container select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .container input[type="checkbox"] {
            margin-right: 10px;
        }

        .container button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }

        .container button:hover {
            background-color: #0056b3;
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

            .container button {
                padding: 8px 12px;
                font-size: 0.9em;
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
        <h1>Configurações</h1>
        <form method="POST">
            <label for="tema">Tema:</label>
            <select id="tema" name="tema">
                <option value="claro" <?php echo $configuracoes['tema'] === 'claro' ? 'selected' : ''; ?>>Claro</option>
                <option value="escuro" <?php echo $configuracoes['tema'] === 'escuro' ? 'selected' : ''; ?>>Escuro</option>
            </select>
            
            <label for="notificacoes">Receber Notificações:</label>
            <input type="checkbox" id="notificacoes" name="notificacoes" <?php echo $configuracoes['notificacoes'] ? 'checked' : ''; ?>>
            
            <button type="submit">Salvar</button>
        </form>
    </div>
    <footer class="footer">
        <p>&copy; 2024 Sicron</p>
    </footer>
</body>
</html>
