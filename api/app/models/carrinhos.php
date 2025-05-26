<?php

class Carrinho{

    private $conn;

    public function __construct(){
        $this->conn = new PDO(
            "mysql:host={$_ENV['DB_HOST']}; dbname={$_ENV['DB_NAME']}",
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    public function listarItensCarrinho($id_carrinho){
        $sql = "SELECT 
                    ic.quantidade,
                    ic.preco_unitario,
                    p.id AS produto_id,
                    p.descricao,
                    p.preco,
                    p.estoque,
                    p.categoria_id,
                    p.status_produto
                FROM Itens_Carrinho ic
                INNER JOIN Produtos p ON ic.produto_id = p.id
                WHERE ic.carrinho_id = :id_carrinho";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_carrinho', $id_carrinho, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criarCarrinho($id_usuario){
        $stmt = $this->conn->prepare(
            "INSERT INTO Carrinhos(usuario_id)
            VALUES(:id_usuario)"
        );
        $stmt->bindParam(':id_usuario', $id_usuario);
        return $stmt->execute();

    }
    
    public function buscarCarrinho($id_usuario){
        $stmt = $this->conn->prepare("SELECT * FROM Carrinhos WHERE usuario_id = :id_usuario AND status_carrinho = 'aberto' LIMIT 1");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    // verificar se o item especifico já esta no carrinho
    public function buscarItemCarrinho($id_carrinho, $id_produto){

        $stmt = $this->conn->prepare("SELECT * FROM Itens_Carrinho WHERE produto_id = :id_produto AND carrinho_id = :id_carrinho");
        $stmt->bindParam(':id_produto', $id_produto);
        $stmt->bindParam(':id_carrinho', $id_carrinho);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function adicionarItemCarrinho($id_carrinho, $id_produto, $quantidade, $preco){
        $stmt = $this->conn->prepare(
            "INSERT INTO Itens_Carrinho(carrinho_id, produto_id, quantidade, preco_unitario) 
            UPDATE (:id_carrinho, :id_produto, :quantidade, :preco )
            "
        );
        $stmt->bindParam(':id_carrinho', $id_carrinho);
        $stmt->bindParam(':id_produto', $id_produto);
        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->bindParam(':preco', $preco);
        return $stmt->execute();

    }

    public function atualizarItemCarrinho($id_carrinho, $id_produto, $quantidade, $preco){
        $stmt = $this->conn->prepare(
            "UPDATE Itens_Carrinho
            SET quantidade = :quantidade, preco_unitario = :preco
            WHERE carrinho_id = :id_carrinho and produto_id = :id_produto
            "
        );

        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':id_carrinho', $id_carrinho);
        $stmt->bindParam(':id_produto', $id_produto);
        return $stmt->execute();

    }

    public function excluirItemCarrinho($id_carrinho, $id_produto){
        $stmt = $this->conn->prepare("DELETE FROM Itens_Carrinho WHERE carrinho_id = :id_carrinho AND produto_id = :id_produto");
        $stmt->bindParam(':id_carrinho', $id_carrinho);
        $stmt->bindParam(':id_produto', $id_produto);
        return $stmt->execute();
    }

}
?>