<?php
require_once __DIR__ . '/../models/produtos.php';  

class ProdutoController{

    private $produtoModel;

    public function __construct(){
        $this->produtoModel = new Produto();
    }

    
    public function listar(){
        header('Content-Type: application/json');
        $produtos = $this->produtoModel->buscarTodos();
        echo json_encode([
            'status' => 'success',
            'data' => $produtos
        ]);
        exit;
    }

    public function buscarPorId() {
        header('Content-Type: application/json');

        // Pegando o ID do produto via GET (ex: ?id=10)
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID do produto não fornecido'
            ]);
            exit();
        }

        $produto = $this->produtoModel->buscarPorId($id);

        if ($produto) {
            echo json_encode([
                'status' => 'success',
                'data' => $produto
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Produto não encontrado'
            ]);
    }

    exit();
    }

    public function buscarPorFiltro(){
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $descricao = $data['descricao'] ?? '';
        $categoria = $data['categoria'] ?? '';
        $fornecedor = $data['fornecedor'] ?? '';


        $produtos =$this->produtoModel->buscaPorFiltro($descricao ,$fornecedor, $categoria);
        
        if($produtos){
            echo json_encode([
                'status' => 'success',
                'data' => $produtos
            ]);
        }else{
            echo json_encode([
                'status' => 'Error',
                'messege' => 'Produto não encontrado'
            ]);
        }
        exit();

    }

    public function registrarProduto(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $data = json_decode(file_get_contents('php://input'), true);

            $descricao = $data['descricao'];
            $preco = (float)$data['preco'];
            $estoque = (int)$data['estoque'];
            $categoria_id = (int)$data['categoria_id'];
            $fornecedor_id = (int)$data['fornecedor_id'];

            
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

    public function editarProduto(){
    header('Content-Type: application/json');

    // Captura método atual
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'PUT') {
        // PUT: pegar dados do corpo JSON
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)($data['id'] ?? null);

        if (!$id) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'ID não informado'
            ]);
            return;
        }

        // Coleta dados
        $descricao = $data['descricao'] ?? '';
        $preco = (float)($data['preco'] ?? 0);
        $estoque = (int)($data['estoque'] ?? 0);
        $status_produto = $data['status_produto'] ?? '';
        $categoria_id = (int)($data['categoria_id'] ?? null);
        $fornecedor_id = (int)($data['fornecedor_id'] ?? null);
        $imagem_url = $data['imagem'] ?? null;

        // Atualiza produto
        $sucesso = $this->produtoModel->update($id, $descricao, $preco, $estoque, $status_produto, $categoria_id, $fornecedor_id, $imagem_url);

        echo json_encode([
            'status' => $sucesso ? 'success' : 'error',
            'message' => $sucesso ? 'Produto atualizado com sucesso' : 'Erro ao atualizar o produto'
        ]);
    } else if ($method === 'GET') {
        // GET: pegar ID da URL
        $id = (int)($_GET['id'] ?? null);

        if (!$id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID não informado'
            ]);
            return;
        }

        $produto = $this->produtoModel->buscarPorId($id);

        if ($produto) {
            echo json_encode([
                'status' => 'success',
                'data' => $produto
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Produto não encontrado'
            ]);
        }
    } else {
        // Método não permitido
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => 'Método não permitido'
        ]);
    }

    exit();
}


    public function excluir() {
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] !== 'delete'){
            http_response_code(405);
            echo json_encode([
                'status'=>'Error',
                'messege'=>'Método não permitido'
            ]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)$data['id'] ?? '';
    
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