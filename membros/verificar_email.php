<?php
session_start();
require '../Dao.php';

$dao = new Dao();
$pdo = $dao->getPdo();

// pegar e-mail e board_id do POST
$email = $_POST['email'];
$board_id = $_POST['board_id'];

// buscar o usuário pelo e-mail
$user = $dao->verificarUsuarioPorEmail($email);

if ($user) {
    // verificar se o usuário já é membro do quadro
    $stmt = $pdo->prepare('SELECT * FROM membros_quadro WHERE board_id = :board_id AND user_id = :user_id');
    $stmt->execute(['board_id' => $board_id, 'user_id' => $user['cod']]);
    $isMember = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($isMember) {
        echo 'O usuário já é membro deste quadro.';
    } else {
        echo 'Usuário encontrado: ' . htmlspecialchars($user['nome']) . '<br>';
        echo '<form action="adicionar_membro.php" method="post">
                <input type="hidden" name="board_id" value="' . htmlspecialchars($board_id) . '">
                <input type="hidden" name="user_id" value="' . htmlspecialchars($user['cod']) . '">
                <button type="submit">Adicionar Membro</button>
              </form>';
    }
} else {
    echo 'Não foi encontrado nenhum usuário com este e-mail.';
}
?>
