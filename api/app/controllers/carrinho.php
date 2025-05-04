<?php
require_once __DIR__ . '/../models/carrinhos.php';


class carrinho{
    private $carrinhoModel ;

    public function __construct(){
        $this->carrinhoModel = new carrinho();

    } 

    public function listarItemCarrinho(){
        header('Content-type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id_carrinho = (int)$data['id_carrinho'] ?? '';

        if(!$id_carrinho){
            echo json_encode([
                'status'=>'Error',
                'message'=>'ID do carrinho é obrigatório'
            ]);
            return;
        }

        $lista_item = $this->carrinhoModel->listarItensCarrinho($id_carrinho);

        if($lista_item){
            echo json_encode([
                'status'=>'success',
                'data'=>$lista_item
            ]);
        }else{
            echo json_encode([
                'status'=>'Error',
                'message'=>'Carrinho está vazio'
            ]);
        }
        exite();
    }

    public function adicionarItem(){
        
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $id_produto = (int)$data['id_produto'] ?? '';
            $id_carrinho = (int)$data['id_carrinho'] ?? '';
            $quantidade = (int)$data['quantidade'] ?? '';
            $preco = (float)$data['preco'] ?? '';

            $item = $this->carrinhoModel->buscarItemCarrinho($id_carrinho, $id_produto);

            if(!$item){
                $novoproduto = $this->carrinhoModel->adicionarItemCarrinho($id_carrinho, $id_produto, $quantidade, $preco);

                if($novoproduto){
                    echo json_encode([
                        'status'=>'success',
                        'message'=>'Item adicionado com sucesso'
                    ]);
                }
            }else{

                // codigo duplicado UNIFICAR
                if ($quantidade < 0){
                    $nova_quantidade = $item['quantidade'] + $quantidade;
                    $novo_preco = $item['preco_unitario'] * $nova_quantidade;
                    $novoproduto = $this->carrinhoModel->atualizarItemCarrinho($id_carrinho, $id_produto, $nova_quantidade, $novo_preco);

                    if($novoproduto){
                        echo json_encode([
                            'status'=>'success',
                            'message'=>'Item atualizado com sucesso'
                        ]);
                    }

                }else{
                    $nova_quantidade = $item['quantidade'] + $quantidade;
                    $novo_preco = $item['preco_unitario'] * $nova_quantidade;
                    $novoproduto = $this->carrinhoModel->atualizarItemCarrinho($id_carrinho, $id_produto, $nova_quantidade, $novo_preco);

                    if($novoproduto){
                        echo json_encode([
                            'status'=>'success',
                            'message'=>'Item atualizado com sucesso'
                        ]);
                    }
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