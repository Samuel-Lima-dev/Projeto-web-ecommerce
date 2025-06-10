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
    $sql = "SELECT p.*, MIN(i.caminho_imagem) AS caminho_imagem 
            FROM produtos p
            LEFT JOIN imagens i ON p.id = i.produto_id
            GROUP BY p.id";
    
    $stmt = $this->conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Buscar produto específico pelo seu ID
public function buscarPorId($id){
    $sql = "SELECT p.*, MIN(i.caminho_imagem) AS caminho_imagem 
            FROM produtos p
            LEFT JOIN imagens i ON p.id = i.produto_id
            WHERE p.id = :id
            GROUP BY p.id
            LIMIT 1";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Buscar produtos por filtros (descricao, fornecedor, categoria)
public function buscaPorFiltro($descricao = '', $fornecedorNome = '', $categoriaNome = '') {
    $sql = "SELECT p.*, MIN(i.caminho_imagem) AS caminho_imagem 
            FROM produtos p 
            INNER JOIN categorias c ON p.categoria_id = c.id
            INNER JOIN fornecedores f ON p.fornecedor_id = f.id
            LEFT JOIN imagens i ON p.id = i.produto_id
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

    $sql .= " GROUP BY p.id";

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

    public function update($id, $descricao, $preco, $estoque, $status_produto, $categoria_id, $fornecedor_id, $imagem_caminho) {
    try {
        $this->conn->beginTransaction();

        // Atualizar dados do produto
        $stmt = $this->conn->prepare(
            "UPDATE Produtos 
             SET descricao = :descricao, preco = :preco, estoque = :estoque, 
                 status_produto = :status_produto, categoria_id = :categoria, fornecedor_id = :fornecedor 
             WHERE id = :id"
        );
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':estoque', $estoque, PDO::PARAM_INT);
        $stmt->bindParam(':status_produto', $status_produto);
        $stmt->bindParam(':categoria', $categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(':fornecedor', $fornecedor_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Se imagem foi enviada, trata imagem
        if ($imagem_caminho !== null) {
            // Verifica se já existe imagem para esse produto
            $check = $this->conn->prepare("SELECT id FROM Imagens WHERE produto_id = :produto_id");
            $check->bindParam(':produto_id', $id, PDO::PARAM_INT);
            $check->execute();

            if ($check->rowCount() > 0) {
                // Atualiza a imagem existente
                $stmtImg = $this->conn->prepare(
                    "UPDATE Imagens 
                     SET caminho_imagem = :caminho 
                     WHERE produto_id = :produto_id"
                );
            } else {
                // Insere nova imagem
                $stmtImg = $this->conn->prepare(
                    "INSERT INTO Imagens (produto_id, caminho_imagem) 
                     VALUES (:produto_id, :caminho)"
                );
            }

            $stmtImg->bindParam(':produto_id', $id, PDO::PARAM_INT);
            $stmtImg->bindParam(':caminho', $imagem_caminho);
            $stmtImg->execute();
        }

        $this->conn->commit();
        return true;

    } catch (Exception $e) {
        $this->conn->rollBack();
        return false;
    }
}



    public function excluir($id){
        $stmt = $this->conn->prepare("DELETE FROM Produtos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute(); 
    }


}



?>