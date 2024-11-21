<?php
if(isset($_POST['submit'])){

    include_once "conexao.php";
    include_once "Dao.php";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <style>
        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Estilos gerais */
    body {
        font-family: Arial, sans-serif;
        background: #fff;
        color: #333;
    }

    nav {
        background-color: #007bff;
        color: white;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    nav .logo {
        font-size: 1.5em;
    }

    nav ul {
        list-style: none;
        display: flex;
        flex-wrap: wrap;
    }

    nav ul li {
        margin-left: 10px;
    }

    nav a {
        color: white;
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 4px;
    }

    nav a:hover {
        background-color: #0056b3;
    }

    .center {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 80vh;
        padding: 20px;
    }

    .welcome-text {
        max-width: 600px;
        text-align: center;
    }

    .welcome-text h1 {
        margin-bottom: 20px;
    }

    .welcome-text .bv {
        font-size: 1.2em;
        line-height: 1.6;
    }

    .footer {
        background-color: #007bff;
        color: white;
        text-align: center;
        padding: 10px;
        position: relative;
        bottom: 0;
        width: 100%;
    }

    /* Media Queries para Responsividade */
    @media (max-width: 768px) {
        nav ul {
            flex-direction: column;
            align-items: center;
        }

        nav ul li {
            margin: 5px 0;
        }

        .center {
            flex-direction: column;
        }

        .welcome-text .bv {
            font-size: 1em;
        }
    }

    @media (max-width: 480px) {
        nav .logo {
            font-size: 1.2em;
        }

        .welcome-text h1 {
            font-size: 1.5em;
        }

        .welcome-text .bv {
            font-size: 0.9em;
        }
    }
    </style>
    </head>

    <body>
        <div class="wrapper">
        <h1>CADASTRO</h1>
            <form action="insert_user.php" method="POST">
               
                <label for="Nome" class="labelinput">Nome:</label>
                <input type="text" class="inputuser" id="Nome" name="nome" required>
                <br>
                <label for="Email" class="labelinput">Email:</label>
                <input type="email" class="inputuser" id="Email" name="email" placeholder="...@etec.sp.gov.br" required>
                <br>
                <label for="inputSenha" class="labelinput">Senha:</label>
                <input type="password" class="inputuser" id="Senha" name="senha" placeholder="Crie uma senha..." required>
                <br>
                <button  name="submit">Enviar</button>
                <div class="member">Já tem uma conta? <a href="login.php">Login</a></div>
                </form>
        </div>
        
    </body>
    </html>