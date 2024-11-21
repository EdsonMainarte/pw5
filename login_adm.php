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
    </head>
    
    <body>
        <div class="inputbox">
        <p><a href="index.php">Voltar</a></p>
        </div>
        <div class="wrapper">
            <form action="teste_login_adm.php" method="POST">
                    <h1>LOGIN ADMINISTRADOR</h1>
        
                    <label for="Email" class="labelinput">Email:</label>
                    <input type="email" class="inputuser" id="Email" name="email_adm" required>
                    <br>
                    <label for="inputSenha" class="labelinput">Senha:</label>
                    <input type="password" class="inputuser" id="Senha" name="senha_adm"  required>
                    <br>
                    <button type="submit" class="submit" id="submit" name="submit">Enviar</button>

                </form>
        </div>
    </body>
    </html>