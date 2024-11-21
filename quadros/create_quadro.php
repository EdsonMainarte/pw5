<?php
require '../Dao.php'; // Certifique-se de que o caminho está correto para seu arquivo Dao.php

$dao = new Dao();
$pdo = $dao->getPdo();

session_start();
if (!isset($_SESSION['email'])) {
    die("Usuário não está logado. Redirecionando para login...");
    header('Location: login.php');
    exit();
}

$user_email = $_SESSION['email'];

// Obter o user_id do usuário logado
$stmt = $pdo->prepare("SELECT cod FROM users WHERE email = :email");
$stmt->execute(['email' => $user_email]);
$user = $stmt->fetch();

if ($user) {
    $user_id = $user['cod']; // ID do usuário logado
} else {
    // Se não encontrar o usuário, redireciona para a página de login
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coleta dos dados do formulário
    $nome_quadro = $_POST['nome_quadro'];
    $descricao_quadro = $_POST['descricao_quadro'] ?? ''; // Pode ser vazio
    $cor_quadro = $_POST['cor_quadro'] ?? '#ffffff'; // Cor padrão se não selecionada
    $foto_quadro = null; // Inicializa a variável para foto

    // Verificar se o arquivo foi enviado e se é uma imagem válida
    if (isset($_FILES['foto_quadro']) && $_FILES['foto_quadro']['error'] == 0) {
        $foto_quadro = $_FILES['foto_quadro'];
        
        // Gerar nome único para o arquivo
        $ext = pathinfo($foto_quadro['name'], PATHINFO_EXTENSION);
        $nome_arquivo = uniqid() . '.' . $ext;

        // Diretório onde as imagens serão armazenadas
        $diretorio_upload = '../uploads/';

        // Verificar se o diretório existe, se não, criar
        if (!is_dir($diretorio_upload)) {
            mkdir($diretorio_upload, 0777, true);
        }

        // Verifica o tipo de arquivo da imagem
        $ext = strtolower($ext); // Para garantir que a extensão esteja em minúsculo
        $extensoes_validas = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $extensoes_validas)) {
            // Mover o arquivo para o diretório de uploads
            if (move_uploaded_file($foto_quadro['tmp_name'], $diretorio_upload . $nome_arquivo)) {
                // A imagem foi carregada com sucesso, agora salva no banco
                $foto_quadro = $diretorio_upload . $nome_arquivo;
            } else {
                // Se houver erro no upload, define a foto como null
                $foto_quadro = null;
            }
        } else {
            // Se a extensão do arquivo for inválida
            echo "Tipo de arquivo inválido. Somente imagens JPG, JPEG, PNG e GIF são permitidas.";
            $foto_quadro = null;
        }
    }

    // Inserir os dados no banco de dados
    try {
        $stmt = $pdo->prepare("INSERT INTO quadros (nome_quadro, descricao_quadro, cor_quadro, foto_quadro, user_id) 
                               VALUES (:nome_quadro, :descricao_quadro, :cor_quadro, :foto_quadro, :user_id)");
        $stmt->execute([
            'nome_quadro' => $nome_quadro,
            'descricao_quadro' => $descricao_quadro,
            'cor_quadro' => $cor_quadro,
            'foto_quadro' => $foto_quadro, // Caminho da imagem ou null
            'user_id' => $user_id
        ]);
        
        // Redireciona após a criação do quadro
        header("Location: ../sistema.php");
        exit();
    } catch (Exception $e) {
        echo "Erro ao inserir no banco de dados: " . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar Quadro</title>
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
        <h1>Criar Novo Quadro</h1>
        <form method="post" action="create_quadro.php" enctype="multipart/form-data">
            <input type="text" name="nome_quadro" placeholder="Nome do Quadro" required>
            <textarea name="descricao_quadro" placeholder="Descrição do Quadro" rows="4"></textarea>
            
            <div class="form-group">
                <label for="border-color">Escolha a cor da borda:</label>
                <input type="color" id="border-color" name="cor_quadro" value="#0079bf">
            </div>

            <div class="form-group">
                <label for="foto_quadro">Foto de Capa</label>
                <input type="file" id="foto_quadro" name="foto_quadro" accept="image/*">
            </div>

            <button type="submit">Criar Quadro</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2024 Sicron</p>
    </footer>
    <script>
        // Atualiza a cor de visualização conforme o seletor de cor é alterado
        function updateColorPreview(color) {
            document.getElementById('colorPreview').style.backgroundColor = color;
        }

        // Inicializa a visualização da cor ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            updateColorPreview(document.getElementById('cor_quadro').value);
        });
    </script>
</body>
</html>
