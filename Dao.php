<?php

class Dao
{

    private $dsn;
    private $username;
    private $password;
    private $pdo;

    public function __construct()
    {
        $this->dsn = "mysql:host=localhost;dbname=planejamento";
        $this->username = "root";
        $this->password = "";
        try {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Adicione isto para melhor depuração
        } catch (Exception $e) {
            echo "Erro ao conectar no banco de dados: " . $e->getMessage();
        }
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function cadastrar($nome, $username, $password){
        $stmt = $this->pdo->prepare("INSERT INTO users (nome, email, senha) VALUES (:nome, :email, :senha)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $username);
        $stmt->bindParam(':senha', $password);
        try{
            $stmt->execute();
            return 1;
        }
        catch(PDOException $e){
            return 0;
        }
    }

    public function verificarLogin($username, $password) {
        $stmt = $this->pdo->query("SELECT * FROM users where email=$username and  senha=$password");
        $login = $stmt->fetch();
        if ($login) {
            if ($username == $login['email'] && $password == $login['senha']) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function verificarLoginAdm ($email_adm, $senha_adm){
        $stmt = $this->pdo->query("SELECT * FROM adm where email_adm=$email_adm and senha_adm=$senha_adm");
        $login_adm = $stmt->fetch();
        if ($login_adm) {
            if ($email_adm == $login_adm['email_adm'] && $senha_adm == $login_adm['senha_adm']) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function verificarUsuarioPorEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT cod, nome FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function adicionarMembroAoQuadro($board_id, $user_id)
    {
        $stmt = $this->pdo->prepare("INSERT INTO membros_quadro (board_id, user_id) VALUES (:board_id, :user_id)");
        $stmt->bindParam(':board_id', $board_id);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
}

