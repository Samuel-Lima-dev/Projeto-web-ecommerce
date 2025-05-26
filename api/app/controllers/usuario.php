<?php 
require_once __DIR__ . '/../models/usuarios.php';
require_once __DIR__ . '/../models/carrinhos.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class UsuarioController{

    private $userModel;
    private $carrinhoModel;
    private $secretkey;

    public function __construct(){
        $this-> userModel = new Usuario();
        $this-> carrinhoModel = new Carrinho();
        $this-> secretkey = $_ENV['JWT_SECRET'];
    }

    public function criarConta(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = json_decode(file_get_contents('php://input'), true);

            $nome = $data['nome'] ?? '';
            $email = $data['email'] ?? '';
            $senha = $data['senha'] ?? '';
            $cpf = $data['cpf'] ?? '';

            if(empty($nome) || empty($email) || empty($senha) || empty($cpf)){
                echo json_encode([
                    'status'=>'error',
                    'message'=>'Os campos são obrigatórios'
                ]);
                return;
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                echo json_encode([
                    'status'=>'Error',
                    'message'=>'Email inválido'
                ]);
                return;
            }

            $usuario = $this->userModel->buscaClienteCadastrado($email);

            if($usuario){
                echo json_encode([
                    'status'=>'Erro',
                    'message'=>'Email já está cadastrado'
                ]);
                return;
            }

            $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);
            $novocliente = $this->userModel->criarUsuario($nome, $email, $senha_criptografada, $cpf);

            if($novocliente){
                echo json_encode([
                    'status'=>'success',
                    'message'=>'Cliente cadastrado com sucesso'
                ]);
            }else{
                echo json_encode([
                    'status'=>'Error',
                    'message'=>'Erro ao cadastrar novo cliente'
                ]);
            }
            exit();
        }
        
    }

    public function login(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = json_decode(file_get_contents('php://input'), true);

            $email = $data['email'] ?? '';
            $password = $data['senha'] ?? '';

            if(empty($email) || empty($password)){
                echo json_encode([
                    'status'=> 'error',
                    'message'=>'Os campos são obrigatórios'
                ]);
                return;
            }

            $usuario = $this->userModel->buscaClienteCadastrado($email);

            if(!$usuario || !password_verify($password, $usuario['senha'])){
                echo json_encode([
                    'status'=>'Error',
                    'message'=>'Credenciais inválidas ou usuario não cadastrado'
                ]);
                return;
            }

            $carrinho = $this->carrinhoModel->buscarCarrinho($usuario['id']);

            if($carrinho && $carrinho['status_carrinho'] === 'aberto'){
                $carrinho_id = $carrinho['id'];
            }else{
                $carrinho_id = $this->carrinhoModel->criarCarrinho($usuario['id']);
            }

            $payload=[
                'id' => $usuario['id'],
                'user_name' =>$usuario['nome'],
                'email' => $usuario['email'],
                'carrinho'=>$carrinho_id,
                'exp' => time() + (60 * 60) // 1 hora
            ];
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

            echo json_encode([
                'status' => 'success',
                'message' => 'Login realizado com sucesso',
                'token' => $jwt
            ]);
            exit();
        }
    }

}

?>