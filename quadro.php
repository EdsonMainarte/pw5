<?php
require 'Dao.php';
//include 'configTema.php';

$dao = new Dao();
$pdo = $dao->getPdo(); // Conexão com o banco de dados

session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['email'])) {
    // Se não estiver logado, redireciona para a página inicial (index.php)
    unset($_SESSION['email']); // Pode limpar a variável de sessão para garantir que não fique suja
    header('location: index.php');
    exit();
}

// Se a sessão foi iniciada corretamente, continue com o restante do código
$logado = $_SESSION['email'];

// Verifica se o ID do quadro foi passado e é um número
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de quadro inválido.";
    exit();
}

$quadro_id = (int)$_GET['id'];

// Obter os detalhes do quadro
$stmt = $pdo->prepare("SELECT * FROM quadros WHERE id_quadro = :id_quadro");
$stmt->execute(['id_quadro' => $quadro_id]);
$quadro = $stmt->fetch();

if (!$quadro) {
    echo "Quadro não encontrado.";
    exit();
}

// Obter todas as listas do quadro
$listas = $pdo->prepare("SELECT * FROM listas WHERE id_quadro = :id_quadro ORDER BY created_at");
$listas->execute(['id_quadro' => $quadro_id]);
$listas = $listas->fetchAll();

// Verificar se o usuário logado é o criador do quadro
$logado = $_SESSION['email'];
$stmt = $pdo->prepare("SELECT cod FROM users WHERE email = :email");
$stmt->execute(['email' => $logado]);
$usuario = $stmt->fetch();
$usuario_id = $usuario['cod'];

$criador = $quadro['user_id'] == $usuario_id;

// Obter os membros do quadro
$members_stmt = $pdo->prepare("
    SELECT u.cod, u.nome, u.email
    FROM membros_quadro mq
    JOIN users u ON mq.user_id = u.cod
    WHERE mq.board_id = :board_id
");
$members_stmt->execute(['board_id' => $quadro_id]);
$members = $members_stmt->fetchAll();

// Obter todos os quadros do usuário
$boards_stmt = $pdo->prepare("SELECT id_quadro, nome_quadro, cor_quadro FROM quadros WHERE user_id = :user_id");
$boards_stmt->execute(['user_id' => $usuario_id]);
$boards = $boards_stmt->fetchAll();

// $chat_id = $dao->getBoardChatId($quadro_id);

// if ($chat_id === false) {
//     echo "Chat não encontrado para este quadro.";
//     exit();
// }

//$mensagens_chat = $dao->getBoardMessages($id_quadro);
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            display: flex;
            overflow-x: hidden;
            min-height: 100vh; /* Garante que o corpo da página tenha pelo menos a altura da janela de visualização */
            flex-direction: column;
        }
        nav {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            width: 100%;
            z-index: 1;
            position: relative;
        }

        .logo {
            font-size: 1.5em;
            font-weight: bold;
            margin-right: auto;
        }

        .menu-icon {
            font-size: 1.5em;
            cursor: pointer;
            margin-right: 20px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-left: auto; /* Alinha a barra de pesquisa à direita */
            max-width: 400px; /* Ajuste o valor conforme necessário */
            flex: 1;
        }

        .search-bar input {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px; /* Cantos arredondados */
            outline: none;
            font-size: 1em;
            width: 100%;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .search-bar input:focus {
            border-color: #0056b3;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        .search-results {
            position: absolute;
            top: 100%; /* Fica logo abaixo do campo de pesquisa */
            left: 70%;
            right: 0;
            max-height: 300px; /* Altura máxima para que o resultado não ocupe toda a tela */
            overflow-y: auto; /* Adiciona rolagem se os resultados excederem a altura máxima */
            background-color: #fff; /* Cor de fundo branca para o container de resultados */
            border: 1px solid #ddd; /* Borda para distinguir o container de resultados */
            border-radius: 4px; /* Cantos arredondados */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra leve para destacar o container */
            z-index: 1000; /* Garante que o container de resultados fique acima de outros elementos */
            display: none; /* Inicialmente oculto */
            padding: 10px; /* Espaçamento interno */
            box-sizing: border-box; /* Inclui o padding e a borda no cálculo da largura */
        }

        .search-results h3 {
            margin: 0;
            padding: 10px 0;
            border-bottom: 2px solid #ddd; /* Linha de separação entre seções */
            font-size: 1.2em;
            color: #333;
        }

        .search-results .section {
            margin-bottom: 20px; /* Espaçamento entre seções */
        }

        .search-results .section div {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee; /* Linha de separação entre itens */
        }

        .search-results .icon {
            width: 20px;
            height: 20px;
            margin-right: 10px; /* Espaço entre o ícone e o texto */
            color: #007bff; /* Cor dos ícones */
        }

        .search-results a {
            color: #007bff;
            text-decoration: none;
        }

        .search-results a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .search-bar {
                max-width: 100%;
            }
        }
        .container {
            flex: 1;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            width: 250px;
            background: #f9f9f9;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 2;
        }
        .sidebar.active {
            transform: translateX(0);
        }
        .sidebar .close-btn {
            font-size: 1.5em;
            cursor: pointer;
            color: #333;
            text-align: right;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background: #ddd;
        }
        .sidebar .board-list {
            margin-top: 20px;
        }

        .sidebar .board-list ul {
            list-style: none; /* Remove marcadores da lista */
            padding: 0; /* Remove o padding padrão */
            margin: 0; /* Remove a margem padrão */
            display: flex;
            flex-direction: column; /* Alinha os itens verticalmente */
        }

        .sidebar .board-list li {
            margin-bottom: 10px; /* Espaço entre os itens da lista */
        }

        .sidebar .board-list a {
            display: flex;
            align-items: center;
            text-decoration: none; /* Remove o sublinhado dos links */
            color: #333; /* Cor do texto dos links */
            padding: 10px;
            border-radius: 4px; /* Cantos arredondados */
            background: #f9f9f9; /* Cor de fundo dos itens */
            transition: background-color 0.3s; /* Transição suave para cor de fundo */
        }

        .sidebar .board-list a:hover {
            background-color: #ddd; /* Cor de fundo ao passar o mouse */
        }

        .sidebar .board-list a div {
            background: #007bff; /* Cor do círculo de cor do quadro */
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }
        .sidebar .separator {
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }
        .sidebar .logout {
            color: #e74c3c;
            font-weight: bold;
        }
        .container {
            flex: 1;
            margin-left:200px;
            padding: 20px;
            padding-bottom: 80px; /* Espaço suficiente para o footer */
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .container h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
        }
        .kanban-board {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 10px 0;
            white-space: nowrap; /* Garante que as listas não quebrem para a linha */
        }
        .kanban-list {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            width: 300px; /* Largura fixa para cada lista */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
            display: inline-flex; /* Garante que as listas se alinhem horizontalmente */
            flex-direction: column;
            flex-shrink: 0; /* Impede que as listas encolham */
        }
        .kanban-list:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        .kanban-list h3 {
            margin: 0;
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
            position: relative;
        }
        .kanban-list .menu {
            position: relative;
            cursor: pointer;
        }

        .kanban-list .menu i {
            font-size: 1.5em; /* Tamanho do ícone de menu */
            color: black; /* Cor do ícone de menu */
        }

        .kanban-list .menu-content {
            display: none; /* Inicialmente escondido */
            position: absolute;
            top: 30px; /* Posiciona o menu de opções logo abaixo do ícone */
            right: 0;
            background: #ffffff; /* Cor de fundo do menu */
            border: 1px solid #ddd; /* Borda do menu */
            border-radius: 4px; /* Cantos arredondados */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); /* Sombra do menu */
            z-index: 10; /* Garante que o menu apareça sobre outros elementos */
            min-width: 120px; /* Largura mínima do menu */
            padding: 0; /* Remove o preenchimento padrão */
            font-size: 0.9em; /* Tamanho da fonte dos itens do menu */
        }

        .kanban-list .menu-content.show {
            display: block; /* Exibe o menu quando a classe 'show' está presente */
        }

        .kanban-list .menu-content a {
            display: block;
            padding: 10px;
            color: #3498db;
            text-decoration: none;
            border-bottom: 1px solid #ddd; /* Linha de separação entre itens */
        }

        .kanban-list .menu-content a:last-child {
            border-bottom: none; /* Remove a borda inferior do último item */
        }

        .kanban-list .menu-content a:hover {
            background: #f0f0f0; /* Cor de fundo ao passar o mouse */
        }

        /* Estilo da lista */
        .kanban-list {
            background: #f0f0f0; /* Cor de fundo para diferenciar dos cartões */
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
            display: flex;
            flex-direction: column; /* Garante que os cartões sejam empilhados verticalmente */
        }

        /* Estilo da lista de cartões */
        .kanban-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column; /* Garante que os cartões dentro da lista estejam empilhados verticalmente */
        }

        /* Estilo dos cartões */
        .kanban-list li {
            background: #ffffff; /* Fundo dos cartões */
            padding: 15px;
            margin-bottom: 15px; /* Espaçamento entre cartões */
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        /* Efeito de hover nos cartões */
        .kanban-list li:hover {
            transform: translateY(-3px);
        }
        .kanban-list .card-actions a {
            margin-right: 10px;
            color: #3498db;
            text-decoration: none;
        }
        .kanban-list .card-actions a:hover {
            text-decoration: underline;
        }
        footer {
            text-align: center;
            padding: 15px;
            background: #2c3e50;
            color: white;
            position: relative; /* Altera para 'relative' para que o footer seja posicionado em relação ao fluxo normal do documento */
            width: 100%;
            margin-top: auto; /* Garante que o footer seja empurrado para baixo quando o conteúdo for maior */
        }
        .quadro-actions {
            position: absolute;
            bottom: 10px;
            right: 10px;
        }
        .quadro-actions a {
            display: inline-block;
            margin-left: 10px;
            color: #007bff;
            font-size: 1.2em;
            text-decoration: none;
        }
        .quadro-actions a:hover {
            color: #0056b3;
        }
        .chat-section {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        .chat-messages {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 10px;
            padding: 5px; /* Adiciona um pouco de espaço interno */
        }

        .chat-message {
            margin-bottom: 10px;
            border-radius: 10px;
            padding: 10px;
            display: inline-block;
            max-width: 70%; /* Limita a largura das mensagens */
        }

        .chat-message.mine {
            background-color: #e0f7fa; /* Cor das mensagens do usuário */
            align-self: flex-end; /* Alinha à direita */
            margin-left: auto; /* Faz a margem esquerda automática para empurrar para a direita */
        }

        .chat-message.other {
            background-color: #fff; /* Cor das mensagens de outros usuários */
            align-self: flex-start; /* Alinha à esquerda */
        }

        .timestamp {
            font-size: 0.8em;
            color: #999;
            margin-left: 10px;
        }

        /* Estilo para o container das mensagens */
        .chat-messages {
            display: flex;
            flex-direction: column; /* Coloca as mensagens em coluna */
            gap: 10px; /* Espaçamento entre mensagens */
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
                padding: 15px;
            }
            .menu-icon {
                display: block;
            }
            .container {
                margin-left: 0;
                width: 100%;
            }
            .container ul {
                flex-direction: column;
                gap: 10px;
            }
            .container li {
                min-width: 100%;
            }
            .search-bar {
                max-width: 100%;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //const menuIcon = document.querySelector('.menu-icon');
            const sidebar = document.querySelector('.sidebar');
            const closeBtn = document.querySelector('.sidebar .close-btn');

            menuIcon.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });

            closeBtn.addEventListener('click', function() {
                sidebar.classList.remove('active');
            });
        });
    </script>
</head>
<body>
    <nav>
        <span class="menu-icon">&#9776;</span> <!-- Ícone de menu -->
        <label class="logo">Sicron</label>
        <!-- <div class="search-bar">
            <form class="search-bar" action="search.php" method="GET">
                <input type="text" name="query" placeholder="Pesquisar...">
            </form>
        </div> -->
    </nav>
    <div class="sidebar">
        <div class="close-btn">
            <i class="fas fa-times"></i>
        </div>
        <a href="sistema.php">Inicio</a>
        <a href="quadro.php?id=<?php echo $quadro['id_quadro']; ?>">Visão Geral</a>
        <a href="membros/form_adicionar_membro.php?board_id=<?php echo $quadro_id; ?>">Adicionar Membros</a>
        <div class="board-list">
            <p>Seus Quadros</p>
            <ul>
                <?php foreach ($boards as $board): ?>
                    <li>
                        <a href="quadro.php?id=<?php echo $board['id_quadro']; ?>">
                            <div style="background: <?php echo htmlspecialchars($board['cor_quadro']); ?>;"></div>
                            <span><?php echo htmlspecialchars($board['nome_quadro']); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="separator"></div>
        <a href="sair.php" class="logout">Sair</a>
    </div>
    <div class="container">
        <h1><?php echo htmlspecialchars($quadro['nome_quadro']); ?></h1>
        <section class="kanban-board">
            <?php if (count($listas) > 0): ?>
                <?php foreach ($listas as $lista): ?>
                    <div class="kanban-list">
                        <div class="menu">
                            <i class="fas fa-ellipsis-v"></i> <!-- Ícone de três pontos -->
                            <div class="menu-content">
                                <a href="listas/edit_lista.php?id_quadro=<?php echo $quadro['id_quadro']; ?>&id_lista=<?php echo $lista['id_lista']; ?>">Editar Lista</a>
                                <a href="listas/delete_lista.php?id_quadro=<?php echo $quadro['id_quadro']; ?>&id_lista=<?php echo $lista['id_lista']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta lista?');">Excluir Lista</a>
                            </div>
                        </div>
                        <h3><?php echo htmlspecialchars($lista['nome_lista']); ?></h3>
                        <ul>
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT c.*, u.nome AS responsible_name
                                FROM cartoes c
                                LEFT JOIN users u ON c.membro_responsavel_id = u.cod
                                WHERE c.id_lista = :id_lista
                                ORDER BY c.created_at
                            ");
                            $stmt->execute(['id_lista' => $lista['id_lista']]);
                            $cartoes = $stmt->fetchAll();
                            ?>
                            <?php if (count($cartoes) > 0): ?>
                                <?php foreach ($cartoes as $cartao): ?>
                                    <li>
                                        <strong><?php echo htmlspecialchars($cartao['nome_cartao']); ?></strong>
                                        <?php if ($cartao['responsible_name']): ?>
                                            <p><strong>Responsável:</strong> <?php echo htmlspecialchars($cartao['responsible_name']); ?></p>
                                        <?php else: ?>
                                            <p><strong>Responsável:</strong> Nenhum</p>
                                        <?php endif; ?>
                                        <div class="card-actions">
                                            <a href="cartoes/edit_cartao.php?id_cartao=<?php echo $cartao['id_cartao']; ?>&id_lista=<?php echo $lista['id_lista']; ?>&id_quadro=<?php echo $quadro['id_quadro']; ?>" class="edit-btn" title="Editar"><i class="fas fa-edit"></i></a>
                                            <a href="comentarios.php?id_cartao=<?php echo $cartao['id_cartao']; ?>&id_lista=<?php echo $lista['id_lista']; ?>&id_quadro=<?php echo $quadro['id_quadro']; ?>" class="icon-link" title="Comentarios"><i class="fas fa-comment-dots"></i></a>
                                            <a href="cartoes/delete_cartao.php?id_cartao=<?php echo $cartao['id_cartao']; ?>&id_lista=<?php echo $lista['id_lista']; ?>" class="delete-btn" title="Excluir"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Não há cartões nesta lista.</li>
                            <?php endif; ?>
                        </ul>
                        <a href="cartoes/create_cartao.php?id_lista=<?php echo $lista['id_lista']; ?>" style="color: #3498db;">Adicionar Novo Cartão</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Não há listas neste quadro.</p>
            <?php endif; ?>
        </section>

        <div style="margin-bottom: 40px;">
        <a href="listas/create_lista.php?board_id=<?php echo $quadro['id_quadro']; ?>" style="color: #3498db;">Criar Nova Lista</a>
        </div>

        <section class="members-list">
            <h3>Membros do Quadro</h3>
            <?php if (count($members) > 0): ?>
                <ul class="members-list">
                    <?php foreach ($members as $membro): ?>
                        <li>
                            <span><?php echo htmlspecialchars($membro['email']); ?></span>
                            <?php if ($criador): ?>
                                <a class="delete-icon" onclick="confirmDelete(<?php echo $membro['cod']; ?>, <?php echo $quadro['id_quadro']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php elseif ($membro): ?>
                                <!-- <a href="membros/sair_quadro.php?board_id=<?php echo $quadro['id_quadro']; ?>" class="leave-btn" onclick="return confirm('Tem certeza que deseja sair deste quadro?');" style="color: #e74c3c;">Sair</a> -->
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Não há membros neste quadro.</p>
            <?php endif; ?>
        </section>
    </div>
    <footer class="footer">
        <p>&copy; 2024 Sicron</p>
    </footer>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Abre/fecha o sidebar
    const menuIcon = document.querySelector('.menu-icon');
    const sidebar = document.querySelector('.sidebar');
    const closeBtn = document.querySelector('.sidebar .close-btn');
    menuIcon.addEventListener('click', function() {
        sidebar.classList.toggle('active');
    });
    closeBtn.addEventListener('click', function() {
        sidebar.classList.remove('active');
    });

    // Exibe o menu de opções das listas
    document.querySelectorAll('.kanban-list .menu').forEach(menu => {
        menu.addEventListener('click', () => {
            const menuContent = menu.querySelector('.menu-content');
            menuContent.classList.toggle('show');
        });
    });
});
    </script>
    <script>
        function confirmDelete(userId, boardId) {
            if (confirm("Tem certeza de que deseja remover este membro?")) {
                window.location.href = 'membros/remover_membro.php?user_id=' + userId + '&board_id=' + boardId;
            }
        }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('input[name="query"]');
    const searchBar = document.querySelector('.search-bar');
    const resultsContainer = document.createElement('div');
    resultsContainer.classList.add('search-results');
    searchBar.appendChild(resultsContainer); // Adiciona o container de resultados dentro da barra de pesquisa

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim();

        if (query.length > 0) {
            fetch('search.php?query=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = ''; // Limpa resultados anteriores
                    resultsContainer.style.display = 'block'; // Exibe o container de resultados

                    // Mostrar resultados de quadros
                    if (data.boards.length > 0) {
                        const boardsDiv = document.createElement('div');
                        boardsDiv.classList.add('section');
                        boardsDiv.innerHTML = '<h3>Quadros</h3>';
                        data.boards.forEach(board => {
                            const div = document.createElement('div');
                            div.innerHTML = `<i class="fas fa-columns icon"></i><a href="quadro.php?id=${board.id_quadro}">${board.nome_quadro}</a>`;
                            boardsDiv.appendChild(div);
                        });
                        resultsContainer.appendChild(boardsDiv);
                    }

                    // Mostrar resultados de cartões
                    if (data.cards.length > 0) {
                        const cardsDiv = document.createElement('div');
                        cardsDiv.classList.add('section');
                        cardsDiv.innerHTML = '<h3>Cartões</h3>';
                        data.cards.forEach(card => {
                            const div = document.createElement('div');
                            div.innerHTML = `<i class="fas fa-credit-card icon"></i><a href="cartoes/edit_cartao.php?id_quadro=${card.id_quadro}&id_lista=${card.id_lista}&id_cartao=${card.id_cartao}">${card.nome_cartao}</a>`;
                            cardsDiv.appendChild(div);
                        });
                        resultsContainer.appendChild(cardsDiv);
                    }

                    // Mostrar resultados de membros
                    if (data.members.length > 0) {
                        const membersDiv = document.createElement('div');
                        membersDiv.classList.add('section');
                        membersDiv.innerHTML = '<h3>Membros</h3>';

                        data.members.forEach(member => {
                            const div = document.createElement('div');
                            const link = document.createElement('a');
                            link.innerHTML = `${member.nome}`;
                            link.href = '#'; // Evita redirecionamento padrão

                            // Adiciona o evento de click
                            link.addEventListener('click', (event) => {
                                event.preventDefault(); // Impede o comportamento padrão do link

                                fetch(`chats/get_chat.php?userId1=<?php echo json_encode($user_id); ?>&userId2=${member.cod}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.chatId) {
                                            window.location.href = `chats/chat_view.php?id=${data.chatId}`;
                                        }
                                    })
                                    .catch(error => console.error('Erro:', error));
                            });

                            div.innerHTML = `<i class="fas fa-user icon"></i>`;
                            div.appendChild(link);
                            membersDiv.appendChild(div);
                        });

                        resultsContainer.appendChild(membersDiv);
                    }                    
                });
        } else {
            resultsContainer.style.display = 'none'; // Oculta o container de resultados se a consulta estiver vazia
        }
    });
});

function startChat(memberId) {
    console.log('startChat chamada com ID:', memberId);
    const userId = <?php echo json_encode($user_id); ?>;

    console.log(`Fazendo fetch para: get_chat.php?userId1=${userId}&userId2=${memberId}`);
    fetch(`get_chat.php?userId1=${userId}&userId2=${memberId}`)
        .then(response => {
            console.log('Resposta do fetch:', response);
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos:', data);
            if (data.chatId) {
                window.location.href = `chat_view.php?id=${data.chatId}`;
            } else {
                window.location.href = `create_chat.php?userId1=${userId}&userId2=${memberId}`;
            }
        })
        .catch(error => {
            console.error('Erro no fetch:', error);
        });
}
</script>
</body>
</html>