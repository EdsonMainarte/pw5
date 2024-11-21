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

// Verifica se os IDs foram passados e são numéricos
if (!isset($_GET['id_cartao']) || !is_numeric($_GET['id_cartao']) ||
    !isset($_GET['id_lista']) || !is_numeric($_GET['id_lista'])) {
    echo "ID de cartão ou lista inválido.";
    exit();
}

$cartao_id = (int)$_GET['id_cartao'];
$lista_id = (int)$_GET['id_lista'];

// Obtém o quadro associado à lista
$stmt = $pdo->prepare("SELECT id_quadro FROM listas WHERE id_lista = :id_lista");
$stmt->execute(['id_lista' => $lista_id]);
$lista = $stmt->fetch();

if (!$lista) {
    echo "Lista não encontrada.";
    exit();
}

$quadro_id = $lista['id_quadro'];

// Exclui o cartão
$stmt = $pdo->prepare("DELETE FROM cartoes WHERE id_cartao = :id_cartao");
$stmt->execute(['id_cartao' => $cartao_id]);

// Redireciona para o quadro específico
header("Location: ../quadro.php?id=$quadro_id");
exit();
?>
