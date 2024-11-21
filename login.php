<?php
session_start();
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
            <form action="teste_login.php" method="POST">
                    <h1>LOGIN</h1>
        
                    <label for="Email" class="labelinput">Email:</label>
                    <input type="email" class="inputuser" id="Email" name="email" required>
                    <br>
                    <label for="inputSenha" class="labelinput">Senha:</label>
                    <input type="password" class="inputuser" id="Senha" name="senha"  required>
                    <br>
                    <button type="submit" class="submit" id="submit" name="submit">Enviar</button>
                    <div class="inputbox"><p>Não tem uma conta? <a href="cadastro.php">Cadastre-se já</a></p>
                    </div>
                </form>
        </div>
    </body>
    </html>
    