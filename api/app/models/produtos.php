<?php

class Produto{
    private $conn;

    public function __construct(){

        // Conexão com banco de dados
        $this->conn = new PDO(
            "mysql:host={$_ENV['DB_HOST']}; dbname={$_ENV['DB_NAME']}",
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }

    // Buscar todos os produtos da tabela
    public function buscarTodos(){
        $stmt = $this->conn->query("SELECT * FROM Produtos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Buscar produto especifico pelo o seu ID
    public function buscarPorId($id){
        $stmt = $this->conn->prepare("SELECT * FROM Produtos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscaPorFiltro($descricao ='', $fornecedorNome ='', $categoriaNome ='') {
    $sql = "SELECT p.* FROM produtos p 
            INNER JOIN categorias c ON p.categoria_id = c.id
            INNER JOIN fornecedores f ON p.fornecedor_id = f.id
            WHERE 1=1";
    $params = [];

    if (!empty($descricao)) {
        $sql .= " AND p.descricao LIKE :descricao";
        $params[':descricao'] = '%' . $descricao . '%';
    }

    if (!empty($categoriaNome)) {
        $sql .= " AND c.nome = :categoria";
        $params[':categoria'] = $categoriaNome;
    }
    if (!empty($fornecedorNome)) {
        $sql .= " AND f.nome = :fornecedor";
        $params[':fornecedor'] = $fornecedorNome;
    }

    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // Cadastrar novo produto
    public function inserir($descricao, $preco, $estoque, $categoria_id, $fornecedor_id){
        $stmt = $this->conn->prepare(
            "INSERT INTO Produtos(descricao, preco, estoque, categoria_id, fornecedor_id) 
            VALUES(:descricao, :preco, :estoque, :categoria, :fornecedor)"
        );
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':estoque', $estoque);
        $stmt->bindParam(':categoria', $categoria_id);
        $stmt->bindParam(':fornecedor', $fornecedor_id);
        return $stmt->execute();

    }

    public function update($id, $descricao, $preco, $estoque, $status_produto, $categoria_id, $fornecedor_id){
        $stmt = $this->conn->prepare(
            "UPDATE Produtos 
            SET descricao = :descricao, preco = :preco, estoque = :estoque, status_produto = :status_produto ,categoria_id = :categoria, fornecedor_id = :fornecedor WHERE id = :id"
        );
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':estoque', $estoque, PDO::PARAM_INT);
        $stmt->bindParam(':status_produto', $status_produto);
        $stmt->bindParam(':categoria', $categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(':fornecedor', $fornecedor_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function excluir($id){
        $stmt = $this->conn->prepare("DELETE FROM Produtos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute(); 
    }


}



?>