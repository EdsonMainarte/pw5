<?php
session_start();
//print_r($_REQUEST);

if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha']))
{
    //se existir, acessa.
    include_once('conexao.php');
    $username = $_POST['email'];
    $password = $_POST['senha'];

    /*print_r('Email: '. $username);
    print_r('<br>');
    print_r('Senha: '. $password);*/

    $sql = "SELECT * FROM users WHERE email = '$username'and senha = '$password'";

    $result = $conexao->query($sql);

    /*print_r($sql);
    print_r($result);*/

    if(mysqli_num_rows($result) < 1){
        unset( $_SESSION['email']);
        unset( $_SESSION['senha']);
        header('location: login.php');
    } else {
        $_SESSION['email'] = $username;
        $_SESSION['senha'] = $password;
        header('location: sistema.php');
    }
} 
else
{
    //se não existir, não acessa.
    header('Location: login.php');
}
?>