<?php
require_once __DIR__ . '/../models/produtos.php';  

class ProdutoController{

    private $produtoModel;

    public function __construct(){
        $this->produtoModel = new Produto();
    }

    
    public function listar(){
        header('Content-Type: Application/json');
        $produtos = $this->produtoModel->buscarTodos();
        echo json_encode([
            'status' => 'success',
            'data' => $produtos
        ]);
        exit;
    }

    public function registerProducts(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $descricao = $_POST['descricao'];
            $preco = $_POST['preco'];
            $estoque = $_POST['estoque'];
            $categoria_id = $_POST['categoria_id'];
            $fornecedor_id = $_POST['fornecedor_id'];

            // Validação simples
            if (empty($descricao) || empty($preco) || empty($estoque)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'messege' => 'Todos os campos obrigatórios devem ser preenchidos.'
                ]);
                return;
            }

            $resultado = $this->produtoModel->inserir($descricao, $preco, $estoque, $categoria_id, $fornecedor_id);

            header('Content-Type: application/jason');
            if($resultado){
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Produto cadastrado com sucesso'
                ]);
            }else{
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Erro ao cadastrar produto'
                ]);
            }
            exit();

        }
    }

    public function editProduct(){
        header('Content-Type: application/jason');
        $id = $_GET['id'] ?? null;
        if(!$id){
            echo json_encode([
                'status' => 'error', 
                'message' => 'ID não informado']);
            return;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $descricao = $_POST['descricao'];
            $preco = $_POST['preco'];
            $estoque = $_POST['estoque'];
            $status_produto = $_POST['status_produto'];
            $categoria_id= $_POST['categoria_id'];
            $fornecedor_id = $_POST['fornecedor_id'];

            $sucesso = $this->produtoModel->update($id, $descricao, $preco, $estoque, $status_produto, $categoria_id, $fornecedor_id);

            if ($sucesso) {
                echo json_encode(['status' => 'success', 'message' => 'Produto atualizado com sucesso']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar o produto']);
            }
            
        }else{
            $produto = $this->produtoModel->buscarPorId($id);
            if ($produto) {
                echo json_encode(['status' => 'success', 'data' => $produto]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Produto não encontrado']);
            }
        }
        exit();

    }

    public function excluir() {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;
    
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID não informado']);
            return;
        }
    
        $sucesso = $this->produtoModel->excluir($id);
    
        if ($sucesso) {
            echo json_encode(['status' => 'success', 'message' => 'Produto excluído com sucesso']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir o produto']);
        }
    
        exit();
    }
    


}

?>