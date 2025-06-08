<?php
require_once __DIR__ . '/../models/carrinhos.php';
require_once __DIR__ . '/../auth/JwtService.php';
use Autenticacao\JwtService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class CarrinhoController{
    private $carrinhoModel ;

    public function __construct(){
        $this->carrinhoModel = new carrinho();

    } 

    public function listarItemCarrinho(){
        header('Content-Type: application/json');

        try {
            $auth = JwtService::autenticar();
            $usuarioId = $auth['usuarioId'];
            $carrinhoId = $auth['carrinhoId'];

            $carrinho = $this->carrinhoModel->buscarCarrinho($usuarioId);

            if (!$carrinho) {
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
        try {
            
            $auth = \Autenticacao\JwtService::autenticar();

            $usuarioId = $auth['usuarioId'];

            // Buscar carrinho do usuário
            $carrinho = $this->carrinhoModel->buscarCarrinho($usuarioId);
            if (!$carrinho) {
                echo json_encode(['status' => 'error', 'message' => 'Carrinho não encontrado']);
                return;
            }

            // Validação dos dados
            if (!isset($data['id_produto'], $data['quantidade'], $data['preco'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Parâmetros inválidos.'
                ]);
                return;
            }

            $id_produto = (int) $data['id_produto'];
            $id_carrinho = (int)$carrinho['id'];
            $quantidade = (int) $data['quantidade'];
            $preco = (float) $data['preco'];

            // Verifica se o item já existe no carrinho
            $item = $this->carrinhoModel->buscarItemCarrinho($id_carrinho, $id_produto);

            if (!$item) {
                $novoItem = $this->carrinhoModel->adicionarItemCarrinho($id_carrinho, $id_produto, $quantidade, $preco);

                echo json_encode([
                    'status' => $novoItem ? 'success' : 'error',
                    'message' => $novoItem ? 'Item adicionado com sucesso' : 'Erro ao adicionar item'
                ]);
            } else {
                $nova_quantidade = (int)$item[0]['quantidade'] + $quantidade;

                if ($nova_quantidade <= 0) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Quantidade inválida.'
                    ]);
                    return;
                }

                $novo_preco = $preco * $nova_quantidade; 

                $atualizado = $this->carrinhoModel->atualizarItemCarrinho($id_carrinho, $id_produto, $nova_quantidade, $novo_preco);

                echo json_encode([
                    'status' => $atualizado ? 'success' : 'error',
                    'message' => $atualizado ? 'Item atualizado com sucesso' : 'Erro ao atualizar item'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

    public function excluirItem(){
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode([
                'status' => 'error',
                'message' => 'Método não permitido'
            ]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $auth = \Autenticacao\JwtService::autenticar();

            $usuarioId = $auth['usuarioId'];

            $carrinho = $this->carrinhoModel->buscarCarrinho($usuarioId);
            if (!$carrinho) {
                echo json_encode(['status' => 'error', 'message' => 'Carrinho não encontrado']);
                return;
            }

            $id_produto = (int) ($data['id_produto'] ?? 0);
            $id_carrinho = $carrinho['id'];

            if (!$id_produto) {
                echo json_encode(['status' => 'error', 'message' => 'ID do produto não informado']);
                return;
            }

            $item = $this->carrinhoModel->excluirItemCarrinho($id_carrinho, $id_produto);

            echo json_encode([
                'status' => $item ? 'success' : 'error',
                'message' => $item ? 'Item excluído com sucesso' : 'Erro ao excluir item'
            ]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

   
}
?>