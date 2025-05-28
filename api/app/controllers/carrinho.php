<?php
require_once __DIR__ . '/../models/carrinhos.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class CarrinhoController{
    private $carrinhoModel ;

    public function __construct(){
        $this->carrinhoModel = new carrinho();

    } 

    public function listarItemCarrinho(){
        header('Content-Type: application/json');

        // Obter Authorization header corretamente
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!$authHeader && function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? '';
        }


        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            echo json_encode(['status' => 'error', 'message' => 'Token não enviado']);
            return;
        }

        $jwt = $matches[1];

        try {
            $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], 'HS256'));

            $usuarioId = $decoded->id;
            $carrinhoId = $decoded->carrinho;

            // Buscar carrinho do usuário
            $carrinho = $this->carrinhoModel->buscarCarrinho($usuarioId);

            if(!$carrinho){
                echo json_encode(['status' => 'error', 'message' => 'Carrinho não encontrado']);
                return;
            }

            $itens = $this->carrinhoModel->listarItensCarrinho($carrinho['id']);

            echo json_encode([
                'status' => 'success',
                'carrinho_id' => $carrinho['id'],
                'itens' => $itens
            ]);

        } catch (ExpiredException $e) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Token expirado']);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Token inválido']);
        }
    }

    public function adicionarItem() {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Validação dos dados
        if (!isset($data['id_produto'], $data['id_carrinho'], $data['quantidade'], $data['preco'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Parâmetros inválidos.'
            ]);
            exit();
        }

        $id_produto = (int) $data['id_produto'];
        $id_carrinho = (int) $data['id_carrinho'];
        $quantidade = (int) $data['quantidade'];
        $preco = (float) $data['preco'];

        // Verifica se o item já existe no carrinho
        $item = $this->carrinhoModel->buscarItemCarrinho($id_carrinho, $id_produto);

        if (!$item) {
            // Item novo
            $novoItem = $this->carrinhoModel->adicionarItemCarrinho($id_carrinho, $id_produto, $quantidade, $preco);

            if ($novoItem) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Item adicionado com sucesso'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao adicionar item'
                ]);
            }

        } else {
            // Item já existe – atualizar quantidade e preço
            $nova_quantidade = $item['quantidade'] + $quantidade;

            if ($nova_quantidade <= 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Quantidade inválida.'
                ]);
                exit();
            }

            $novo_preco = $item['preco_unitario'] * $nova_quantidade;

            $atualizado = $this->carrinhoModel->atualizarItemCarrinho($id_carrinho, $id_produto, $nova_quantidade, $novo_preco);

            if ($atualizado) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Item atualizado com sucesso'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao atualizar item'
                ]);
            }
        }
    }

    exit();
}


    public function excluirItem(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] !== 'delete' ){
            http_response_code(405);
            echo json_encode([
                'status'=>'Error',
                'messege'=>'Método não permitido'
            ]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $id_carrinho = (int)$data['id_carrinho'] ?? '';
        $id_produto = (int)$data['id_produto'] ?? '';
        
        if(!$id_carrinho || !$id_produto){
            echo json_encode([
                'status' => 'error', 
                'message' => 'ID não informado'
            
            ]);
            return;
        }
        
        $item = $this->carrinhoModel->excluirItemCarrinho($id_carrinho, $id_produto);

        if ($item) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Item excluído com sucesso']);
        } else {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Erro ao excluir Item']);
        }
        exit();
    }
}

?>