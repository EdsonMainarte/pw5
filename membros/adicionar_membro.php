<?php
session_start();
require '../Dao.php';

$dao = new Dao();

// pegar board_id e user_id do POST
$board_id = $_POST['board_id'];
$user_id = $_POST['user_id'];

// adicionar membro ao quadro
if ($dao->adicionarMembroAoQuadro($board_id, $user_id)) {
    echo 'Membro adicionado com sucesso!';
} else {
    echo 'Erro ao adicionar membro.';
}
?>
