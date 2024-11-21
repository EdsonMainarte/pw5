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

// Obter o user_id do usuário logado
$stmt = $pdo->prepare("SELECT cod FROM users WHERE email = :email");
$stmt->execute(['email' => $logado]);
$user = $stmt->fetch();
$user_id = $user['cod'];

// Verificar se a consulta de pesquisa foi enviada
if (isset($_GET['query'])) {
    $query = $_GET['query'];

    // Inicializar a resposta
    $response = [
        'boards' => [],
        'cards' => [],
        'members' => []
    ];

    // Pesquisa de quadros
    $stmt = $pdo->prepare("
        SELECT id_quadro, nome_quadro, cor_quadro
        FROM quadros
        WHERE nome_quadro LIKE :query AND (user_id = :user_id OR id_quadro IN (
            SELECT board_id FROM membros_quadro WHERE user_id = :user_id
        ))
    ");
    $stmt->execute(['query' => "%$query%", 'user_id' => $user_id]);
    $response['boards'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pesquisa de cartões com id_quadro e id_lista
    $stmt = $pdo->prepare("
        SELECT c.id_cartao, c.nome_cartao, l.id_lista, q.id_quadro
        FROM cartoes c
        JOIN listas l ON c.id_lista = l.id_lista
        JOIN quadros q ON l.id_quadro = q.id_quadro
        WHERE c.nome_cartao LIKE :query AND l.id_quadro IN (
            SELECT id_quadro FROM quadros WHERE user_id = :user_id OR id_quadro IN (
                SELECT board_id FROM membros_quadro WHERE user_id = :user_id
            )
        )
    ");
    $stmt->execute(['query' => "%$query%", 'user_id' => $user_id]);
    $response['cards'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pesquisa de membros
    $stmt = $pdo->prepare("
        SELECT cod, nome
        FROM users
        WHERE nome LIKE :query
    ");
    $stmt->execute(['query' => "%$query%"]);
    $response['members'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enviar resposta em formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Se a consulta não for fornecida, retornar um JSON vazio
    echo json_encode([
        'boards' => [],
        'cards' => [],
        'members' => []
    ]);
}
?>
