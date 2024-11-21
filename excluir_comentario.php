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

$user_email = $_SESSION['email'];

// Captura os parâmetros da URL
$id_comentario = isset($_GET['id_comentario']) ? (int)$_GET['id_comentario'] : null;
$id_cartao = isset($_GET['id_cartao']) ? (int)$_GET['id_cartao'] : null;
$id_lista = isset($_GET['id_lista']) ? (int)$_GET['id_lista'] : null;
$id_quadro = isset($_GET['id_quadro']) ? (int)$_GET['id_quadro'] : null;

if ($id_comentario === null || $id_cartao === null || $id_lista === null || $id_quadro === null) {
    echo "Parâmetros inválidos.";
    exit();
}

// Verifica se o comentário existe e pertence ao usuário
$stmt = $pdo->prepare("SELECT * FROM comentarios WHERE id_comentario = :id_comentario AND user_email = :user_email");
$stmt->execute(['id_comentario' => $id_comentario, 'user_email' => $user_email]);
$comentario = $stmt->fetch();

if (!$comentario) {
    echo "Comentário não encontrado ou você não tem permissão para excluir este comentário.";
    exit();
}

// Remove o comentário
$stmt = $pdo->prepare("DELETE FROM comentarios WHERE id_comentario = :id_comentario");
$stmt->execute(['id_comentario' => $id_comentario]);

header("Location: comentarios.php?id_cartao=$id_cartao&id_lista=$id_lista&id_quadro=$id_quadro");
exit();
