<?php
session_start();
//print_r($_REQUEST);

if(isset($_POST['submit']) && !empty($_POST['email_adm']) && !empty($_POST['senha_adm']))
{
    //se existir, acessa.
    include_once('conexao.php');
    $email_adm = $_POST['email_adm'];
    $senha_adm = $_POST['senha_adm'];

    /*print_r('Email: '. $username);
    print_r('<br>');
    print_r('Senha: '. $password);*/

    $sql = "SELECT * FROM adm WHERE email_adm = '$email_adm'and senha_adm = '$senha_adm'";

    $result = $conexao->query($sql);

    /*print_r($sql);
    print_r($result);*/

    if(mysqli_num_rows($result) < 1){
        unset( $_SESSION['email_adm']);
        unset( $_SESSION['senha_adm']);
        header('location: login_adm.php');
    } else {
        $_SESSION['email_adm'] = $email_adm;
        $_SESSION['senha_adm'] = $senha_adm;
        header('location: pagadm.php');
    }
} 
else
{
    //se não existir, não acessa.
    header('Location: loginadm.php');
}
?>