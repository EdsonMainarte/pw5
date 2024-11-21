<?php
// Inicie a sessão e faça a conexão com o banco de dados
session_start();
require '../Dao.php'; // Ajuste o caminho se necessário

$dao = new Dao();

// Verifique se o board_id foi passado via GET
if (!isset($_GET['board_id']) || empty($_GET['board_id'])) {
    echo 'ID do quadro não fornecido.';
    exit;
}

$board_id = htmlspecialchars($_GET['board_id']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Adicionar Membro - Sicron</title>
    <link rel="stylesheet" href="../style.css">
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
    <h1>Adicionar Membro ao Quadro</h1>
    <form onsubmit="event.preventDefault(); verificarEmail();">
        <label for="email">Digite o e-mail do membro:</label>
        <input type="email" id="email" name="email" required>
        <input type="hidden" id="board_id" name="board_id" value="<?php echo $board_id; ?>" />
        <button type="submit">Verificar</button>
    </form>
    <div id="resultado"></div>
    </div>
    <footer>
        <p>&copy; 2024 Sicron</p>
    </footer>
    <script>
        function verificarEmail() {
            var email = document.getElementById('email').value;
            var boardId = document.getElementById('board_id').value;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'verificar_email.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('resultado').innerHTML = xhr.responseText;
                }
            };
            xhr.send('email=' + encodeURIComponent(email) + '&board_id=' + encodeURIComponent(boardId));
        }
    </script>
</body>
</html>
