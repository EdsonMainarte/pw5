<?php
require '../Dao.php';

$dao = new Dao();
$pdo = $dao->getPdo(); // Conexão com o banco de dados

if (!$pdo) {
    die("Erro na conexão com o banco de dados.");
}

session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('location: index.php');
    exit();
}

if (!isset($_GET['board_id']) || !is_numeric($_GET['board_id'])) {
    echo "Parâmetro inválido.";
    exit();
}

$board_id = (int)$_GET['board_id'];

// Remover o usuário do quadro
$logado = $_SESSION['email'];
$stmt = $pdo->prepare("SELECT cod FROM users WHERE email = :email");
$stmt->execute(['email' => $logado]);
$usuario = $stmt->fetch();
$user_id = $usuario['cod'];

$stmt = $pdo->prepare("DELETE FROM membros_quadro WHERE board_id = :board_id AND user_id = :user_id");
$stmt->execute(['board_id' => $board_id, 'user_id' => $user_id]);

// Redirecionar de volta para a página de quadros do usuário
header("Location: ../quadro.php?id=$board_id");
exit();
?>
