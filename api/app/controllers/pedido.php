<?php

require_once __DIR__ . '/../models/pedidos.php';
require_once __DIR__ . '/../auth/JwtService.php';

use Autenticacao\JwtService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class PedidoController{

    private $pedidoModel ;

    public function __construct(){
        $this->pedidoModel = new Pedido();

    } 

    public function listaPedido(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
            try {
                // Autentica o usuário e retorna o ID
                $auth = \Autenticacao\JwtService::autenticar();
                $usuario_id = $auth['usuarioId'];
                
                $listagemPedidos = $this->pedidoModel->listarPedidos($usuario_id);

                if ($listagemPedidos) {
                    echo json_encode([
                        'status' => 'success',
                        'pedidos' => $listagemPedidos
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode($listagemPedidos);
                }

            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao listar pedidos: ' . $e->getMessage()
                ]);
            }
        }
    
}



    public function finalizarCompra(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $data=json_decode(file_get_contents('php://input'), true);

            try{
                $auth = \Autenticacao\JwtService::autenticar();

                $usuarioId = $auth['usuarioId'];
                $carrinhoId = $auth['carrinhoId'];

                $itensSelecionados = [];
                // garante que seja um array, converte os valores para inteiros e remove IDs inválidos (zeros, negativos ou nulos)
                if (isset($data['itensSelecionados']) && is_array($data['itensSelecionados'])) {
                    $itensSelecionados = array_filter(array_map('intval', $data['itensSelecionados']), fn($id) => $id > 0);
                }
                
                $valorTotal = $this->pedidoModel->calcularValorTotal($usuarioId, $carrinhoId, $itensSelecionados);

                if ($valorTotal === false || $valorTotal === null) {
                    echo json_encode(['status' => 'error', 'message' => 'Valores Incorretos']);
                    return;
                }
                // criação do pedido
                $pedido = $this->pedidoModel->criarPedido($usuarioId, $valorTotal);

                if($pedido){
                    $adicionarItens = $this -> pedidoModel -> finalizarPedido($pedido, $carrinhoId, $usuarioId, $itensSelecionados);
                }

                if($pedido){
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Pedido finalizado com sucesso',
                        'pedido_id' => $pedido
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Erro ao finalizar pedido'
                    ]);
                }
                exit;



            }catch(Exception $e){
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                return;
            }
        
        }else{
            http_response_code(405);
            echo json_encode([
                'status' => 'error',
                'message' => 'Método não permitido'
            ]);
            return;
        }
    }   


    public function finalizarPagamento() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);

            try {
                $auth = JwtService::autenticar();

                $usuarioId = $auth['usuarioId'];
                $carrinhoId = $auth['carrinhoId'];
                $pedidoId = (int)$data['pedido_id'];
                $metodo = $data['metodo'];

                // Registrar pagamento
                $pagamentoRegistrado = $this -> pedidoModel -> registrarPagamento($pedidoId, $metodo);
                if (!$pagamentoRegistrado) {
                    throw new Exception("Falha ao registrar pagamento.");
                }

                // Atualizar estoque
                $estoqueOk = $this->pedidoModel->atualizarEstoque($pedidoId);
                if (!$estoqueOk) {
                    throw new Exception("Falha ao atualizar estoque.");
                }

                // Atualizar status do pedido
                $pedidoAtualizado = $this-> pedidoModel -> atualizarStatusPedido($pedidoId, 'pago');

                // 4. Remover itens do carrinho
                $itensPedido = $this -> pedidoModel -> buscarItensPedido($pedidoId);
                $produtosIds = array_column($itensPedido, 'produto_id');
                $removerCarrinho = $this-> pedidoModel -> removerItensCarrinho($carrinhoId, $produtosIds);


                if($removerCarrinho){
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Pagamento confirmado e pedido atualizado com sucesso.'
                    ]);            
                }


            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
        }
    }


}



?>