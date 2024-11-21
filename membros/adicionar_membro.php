


<?php
session_start();
require '../Dao.php';
 
$dao = new Dao();
 
// pegar board_id e user_id do POST
$board_id = $_POST['board_id'];
$user_id = $_POST['user_id'];
 
// adicionar membro ao quadro
if ($dao->adicionarMembroAoQuadro($board_id, $user_id)) {
    $message = 'Membro adicionado com sucesso!';
    $status = 'success';
} else {
    $message = 'Erro ao adicionar membro.';
    $status = 'error';
}
?>
 
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status de Adição - Sicron</title>
<link rel="stylesheet" href="../style.css">
<style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fb;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
 
        nav {
            background-color: #007bff;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap;
        }
 
        nav .logo {
            font-size: 25px;
            font-weight: bold;
        }
 
        nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
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
 
        .status-container {
            width: 80%;
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
 
        .status-container.success {
            border-left: 5px solid #2ecc71;
        }
 
        .status-container.error {
            border-left: 5px solid #e74c3c;
        }
 
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
 
        p {
            font-size: 18px;
            margin-bottom: 30px;
        }
 
        a {
            color: #3498db;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
        }
 
        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: auto;
        }
 
        /* Responsividade */
        @media (max-width: 768px) {
            nav ul {
                display: block;
                text-align: center;
            }
 
            nav ul li {
                display: inline-block;
                margin: 5px 0;
            }
 
            .status-container {
                width: 90%;
                padding: 20px;
            }
 
            h1 {
                font-size: 22px;
            }
 
            p {
                font-size: 18px;
            }
 
            a {
                font-size: 16px;
            }
        }
</style>
</head>
<body>
 
    <nav>
<div class="logo">
<a href="sistema.php" style="color:white; text-decoration: none;">Sicron</a>
</div>
</nav>
 
    <div class="status-container <?php echo $status; ?>">
<h1><?php echo $message; ?></h1>
<p><a href="../quadro.php?id=<?php echo $board_id; ?>">Voltar ao quadro</a></p>
</div>
 
    <footer>
<p>&copy; 2024 Sicron - Todos os direitos reservados.</p>
</footer>
 
</body>
</html>