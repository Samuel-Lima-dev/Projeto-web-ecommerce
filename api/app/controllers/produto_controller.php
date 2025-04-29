<?php
require_once __DIR__ . '/../models/produtos.php';  

class ProdutoController{

    private $produtoModel;

    public function __construct(){
        $this->produtoModel = new Produto();
    }

    
    public function listar(){
        $produtos = $this->produtoModel->buscarTodos();
        require __DIR__ . '/../views/produtos/listar_produtos.php';
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
                echo "Todos os campos obrigatórios devem ser preenchidos.";
                return;
            }

            $this->produtoModel->inserir($descricao, $preco, $estoque, $categoria_id, $fornecedor_id);

            header('Location: index.php?controller=produto&action=listar');
            exit();

        }
        require __DIR__ . '/../views/produtos/cadastrar.php';
    }

    public function editProduct(){
        $id = $_GET['id'] ?? null;
        if(!$id){
            echo"ID não informado";
            return;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $descricao = $_POST['descricao'];
            $preco = $_POST['preco'];
            $estoque = $_POST['estoque'];
            $status_produto = $_POST['status_produto'];
            $categoria_id= $_POST['categoria_id'];
            $fornecedor_id = $_POST['fornecedor_id'];

            $this->produtoModel->update($id, $descricao, $preco, $estoque, $status_produto, $categoria_id, $fornecedor_id);
            header('Location: index.php?controller=produto&action=listar');
            exit();
        }
        $produto = $this->produtoModel->buscarPorId($id);
        if(!$produto){
            echo"Produto não encontrado";
            return;

        }
        require __DIR__ . '/../views/produtos/editar.php';

    }

    public function excluir(){
        $id = $_GET['id'] ?? null;
        if(!$id){
            echo"ID não informado";
            return;
        }

        $this->produtoModel->excluir($id);
        header('location: index.php?controller=produto&action=listar');
        exit(); 
    }


}

?>