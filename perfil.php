<?php
require 'Dao.php';

$dao = new Dao();
$pdo = $dao->getPdo(); // Conexão com o banco de dados

session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('location: index.php');
    exit();
}

$logado = $_SESSION['email'];

// Obter informações do usuário
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $logado]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualizar informações do perfil
    $nome = $_POST['nome'];

    // Lidar com o upload da foto de perfil
    if (!empty($_FILES['foto_perfil']['name'])) {
        $target_dir = "uploads/"; // Pasta onde as fotos serão armazenadas
        $target_file = $target_dir . basename($_FILES['foto_perfil']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validar se é uma imagem
        $check = getimagesize($_FILES['foto_perfil']['tmp_name']);
        if ($check === false) {
            echo "O arquivo não é uma imagem.";
            exit();
        }

        // Verificar o tamanho do arquivo
        if ($_FILES['foto_perfil']['size'] > 50000000) {
            echo "Desculpe, o arquivo é muito grande.";
            exit();
        }

        // Permitir apenas certos formatos de arquivo
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
            exit();
        }

        // Tentar mover o arquivo para o destino
        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $target_file)) {
            $foto_perfil = $target_file; // Armazenar o caminho do arquivo no banco de dados
        } else {
            echo "Desculpe, houve um erro ao fazer o upload da sua foto.";
            exit();
        }
    } else {
        // Se não houve upload de nova foto, manter a foto antiga
        $foto_perfil = $user['foto_perfil'];
    }

    // Atualizar o nome e a foto no banco de dados
    $stmt = $pdo->prepare("UPDATE users SET nome = :nome, foto_perfil = :foto_perfil WHERE email = :email");
    $stmt->execute(['nome' => $nome, 'foto_perfil' => $foto_perfil, 'email' => $logado]);

    // Atualizar o usuário com a nova imagem
    $user['foto_perfil'] = $foto_perfil;

    // Redirecionar ou mostrar uma mensagem de sucesso
    echo "Perfil atualizado com sucesso!";
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil</title>
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
            font-weight: bold;
            margin-bottom: 5px;
        }

        .container input[type="text"],
        .container input[type="file"],
        .container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .container button {
            background-color: #007bff;
            color: white;
            border: none;
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
        }

        /* Estilo para a imagem do perfil */
        .profile-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
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
        <h1>Seu Perfil</h1>

        <!-- Exibir a foto de perfil se existir -->
        <?php if (!empty($user['foto_perfil'])): ?>
            <img src="<?php echo $user['foto_perfil']; ?>" alt="Foto de Perfil" class="profile-picture">
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>

            <label for="foto_perfil">Foto de Perfil:</label>
            <input type="file" id="foto_perfil" name="foto_perfil">
            
            <button type="submit">Salvar</button>
        </form>
    </div>
    <footer class="footer">
        <p>&copy; 2024 Sicron</p>
    </footer>
</body>
</html>
