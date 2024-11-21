<?php 

require_once "Dao.php";

$dao = new Dao();
$nome = $_POST['nome'];
$username = $_POST["email"];
$password = $_POST["senha"];

$retorno  = $dao->cadastrar($nome, $username, $password);

if($retorno ==1){
    header('location: login.php');
}
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Responde Ai</title>
    <link rel="stylesheet" href="style.css">
    <style>
      body{
            background: #fff;
        }
    </style>
    <nav>
        <label class="logo">RespondeAi</label>
        <ul>
        <li><a href="index.php">VOLTAR</a></li>
        </ul>
        <section>
<div class="center">
    <div class="welcome-text">
    Erro ao cadastrar aluno, esses dados já existem no nosso sistema. Sugerimos que mude seu e-mail.<br>
    <a href="cadastro.php">Retorne ao cadastro.</a> 
    <!-- <svg class="bi mb-2 fs-2 text-body-secondary"><use xlink:href="#globe2"></use></svg> -->
  </div>
</section>
</div>
<footer class="footer">
    <p>&copy; 2023 RespondeAi</p>
  </footer>