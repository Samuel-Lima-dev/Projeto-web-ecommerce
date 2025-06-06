<?php

namespace Autenticacao;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class JwtService {
    public static function autenticar() {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!$authHeader && function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? '';
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new Exception('Token não enviado');
        }

        $jwt = $matches[1];

        try {
            $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], 'HS256'));
            return [
                'usuarioId' => $decoded->id,
                'carrinhoId' => $decoded->carrinho ?? null
            ];
        } catch (ExpiredException $e) {
            throw new Exception('Token expirado');
        } catch (Exception $e) {
            throw new Exception('Token inválido');
        }
    }
}
