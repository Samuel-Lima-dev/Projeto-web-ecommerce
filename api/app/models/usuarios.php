<?php

class Usuario{
    private $conn;

    public function __construct(){

        $this->conn = new PDO(
            "mysql:host={$_ENV['DB_HOST']}; dbname={$_ENV['DB_NAME']}",
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function criarUsuario($nome, $email, $senha, $cpf){
        $stmt = $this->conn->prepare(
            "INSERT INTO Usuarios(nome, email, senha, cpf)
            VALUES(:nome, :email, :senha, :cpf)"
        );

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
    }

    public function buscaClienteCadastrado($email){
        $stmt = $this->conn->prepare("SELECT * FROM Usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}