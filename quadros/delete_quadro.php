<?php
require '../Dao.php';

$dao = new Dao();
$pdo = $dao->getPdo(); // Conexão com o banco de dados

session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('location: ../index.php');
    exit();
}

// Verifica se o ID do quadro foi passado e é um número
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de quadro inválido.";
    exit();
}

$id_quadro = (int)$_GET['id'];

// Verifica se o quadro existe e se é do usuário logado
$stmt = $pdo->prepare("SELECT user_id FROM quadros WHERE id_quadro = :id_quadro");
$stmt->execute(['id_quadro' => $id_quadro]);
$quadro = $stmt->fetch();

if (!$quadro) {
    echo "Quadro não encontrado.";
    exit();
}

$logado = $_SESSION['email'];
$stmt = $pdo->prepare("SELECT cod FROM users WHERE email = :email");
$stmt->execute(['email' => $logado]);
$user = $stmt->fetch();
$user_id = $user['cod'];

if ($quadro['user_id'] !== $user_id) {
    echo "Você não tem permissão para excluir este quadro.";
    exit();
}

// Deleta o quadro
$stmt = $pdo->prepare("DELETE FROM quadros WHERE id_quadro = :id_quadro");
$stmt->execute(['id_quadro' => $id_quadro]);

header("Location: ../sistema.php"); // Redireciona após a exclusão
exit();
?>
