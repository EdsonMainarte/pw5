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

// Verifica se o ID da lista e o ID do quadro foram passados
if (!isset($_GET['id_lista']) || !is_numeric($_GET['id_lista']) || !isset($_GET['id_quadro']) || !is_numeric($_GET['id_quadro'])) {
    echo "ID de lista ou quadro inválido.";
    exit();
}

$lista_id = (int)$_GET['id_lista'];
$quadro_id = (int)$_GET['id_quadro'];

// Exclui a lista
$stmt = $pdo->prepare("DELETE FROM listas WHERE id_lista = :id_lista");
$stmt->execute(['id_lista' => $lista_id]);

header("Location: ../quadro.php?id=$quadro_id");
exit();
?>
