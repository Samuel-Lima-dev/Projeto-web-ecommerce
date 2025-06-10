<?php 

    class Pedido{

        private $conn;

        public function __construct(){
            $this->conn = new PDO(
                "mysql:host={$_ENV['DB_HOST']}; dbname={$_ENV['DB_NAME']}",
                $_ENV['DB_USER'],
                $_ENV['DB_PASS']
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        // listar pedidos
        public function listarPedidos($usuario_id) {
            // Buscar todos os pedidos do usuário
            $sql = "SELECT p.id, p.data_pedido, p.status_pedido, p.total,
                        pg.metodo, pg.status as status_pagamento, pg.data_pagamento
                    FROM Pedidos p
                    LEFT JOIN Pagamentos pg ON p.id = pg.pedido_id
                    WHERE p.usuario_id = :usuario_id
                    ORDER BY p.data_pedido DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['usuario_id' => $usuario_id]);
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pedidos as &$pedido) {
                // Para cada pedido buscar os itens com dados detalhados
                $sqlItens = "SELECT ip.produto_id, ip.quantidade, ip.preco_unitario,
                                    pr.descricao, 
                                    (ip.preco_unitario * ip.quantidade) as valor_total,
                                    im.caminho_imagem
                            FROM Itens_Pedidos ip
                            INNER JOIN Produtos pr ON ip.produto_id = pr.id
                            LEFT JOIN Imagens im ON pr.id = im.produto_id
                            WHERE ip.pedido_id = :pedido_id";

                $stmtItens = $this->conn->prepare($sqlItens);
                $stmtItens->execute(['pedido_id' => $pedido['id']]);
                $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

                // Adiciona os itens ao pedido
                $pedido['itens'] = $itens;
            }

            return $pedidos;
        }

        public function listarItensPedido($pedido_id, $usuario_id) {
            $sql = "SELECT 
                        ip.id,
                        ip.pedido_id,
                        ip.produto_id,
                        ip.quantidade,
                        ip.preco_unitario,
                        p.descricao,
                        MIN(img.caminho_imagem) AS imagem,
                        ped.total AS total_pedido
                    FROM itens_pedidos ip
                    INNER JOIN produtos p ON ip.produto_id = p.id
                    LEFT JOIN imagens img ON p.id = img.produto_id
                    INNER JOIN pedidos ped ON ip.pedido_id = ped.id
                    WHERE ip.pedido_id = :pedido_id AND ped.usuario_id = :usuario_id
                    GROUP BY ip.id, ip.pedido_id, ip.produto_id, ip.quantidade, ip.preco_unitario, p.descricao, ped.total";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        // calcular valor total dos itens
        public function calcularValorTotal($usuarioId, $carrinhoId, $itensSelecionados){

            if(empty($itensSelecionados)){
                return 0;
            }

            $placeholders = implode(',', array_fill(0, count($itensSelecionados), '?'));

            $sql = "
                SELECT SUM(ic.quantidade * ic.preco_unitario) AS total
                FROM Itens_Carrinho ic
                INNER JOIN Carrinhos c ON ic.carrinho_id = c.id
                WHERE c.usuario_id = ? AND c.id = ? AND ic.id IN ($placeholders)
            ";

            $params = array_merge([$usuarioId, $carrinhoId], $itensSelecionados);

            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($resultado === false) {
                    // erro na consulta
                    return false;
                }

                return isset($resultado['total']) ? (float)$resultado['total'] : 0.0;

            } catch (PDOException $e) {
                error_log("Erro calcularValorTotal: " . $e->getMessage());
                return false;
            }
        }


        // criação de pedido
        public function criarPedido($id_usuario, $valor_total_compra){

            $sql = "INSERT INTO Pedidos (usuario_id, total) VALUES (:usuario_id, :total)";
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':usuario_id' => $id_usuario,
                ':total' => $valor_total_compra
            ]);
            // Retorna o ID do pedido criado
            return $this->conn->lastInsertId();
            
        }

        public function registrarPagamento($pedidoId, $metodo) {
            try {
                $stmt = $this->conn->prepare("
                    INSERT INTO Pagamentos (pedido_id, metodo, status, data_pagamento)
                    VALUES (:pedido_id, :metodo, 'aprovado', NOW())
                ");
                return $stmt->execute([
                    ':pedido_id' => $pedidoId,
                    ':metodo' => $metodo
                ]);
            } catch (PDOException $e) {
                return false;
            }
        }

       public function atualizarEstoque($pedidoId) {
            try {
                // Pega os produtos e quantidades do pedido
                $sql = "SELECT produto_id, quantidade 
                        FROM Itens_Pedidos 
                        WHERE pedido_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$pedidoId]);
                $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$itens) {
                    throw new Exception("Nenhum item encontrado no pedido.");
                }

                // Atualiza o estoque para cada produto
                foreach ($itens as $item) {
                    $sqlUpdate = "UPDATE Produtos 
                                SET estoque = estoque - ? 
                                WHERE id = ? AND estoque >= ?";
                    $stmtUpdate = $this->conn->prepare($sqlUpdate);
                    $stmtUpdate->execute([
                        $item['quantidade'], 
                        $item['produto_id'], 
                        $item['quantidade']
                    ]);

                    if ($stmtUpdate->rowCount() === 0) {
                        throw new Exception("Estoque insuficiente para o produto ID " . $item['produto_id']);
                    }
                }

                return true;

            } catch (Exception $e) {
                throw new Exception("Falha ao atualizar estoque: " . $e->getMessage());
            }
        }


        public function atualizarStatusPedido($pedidoId, $status) {
            try {
                $stmt = $this->conn->prepare("
                    UPDATE Pedidos 
                    SET status_pedido = :status 
                    WHERE id = :pedido_id
                ");
                return $stmt->execute([
                    ':status' => $status,
                    ':pedido_id' => $pedidoId
                ]);
            } catch (PDOException $e) {
                return false;
            }
        }

        public function buscarItensPedido($pedidoId) {
            $stmt = $this->conn->prepare("SELECT produto_id FROM Itens_Pedidos WHERE pedido_id = ?");
            $stmt->execute([$pedidoId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        public function removerItensCarrinho($carrinhoId, $produtosIds) {
            try {
                if (empty($produtosIds)) {
                    throw new Exception("Nenhum produto selecionado para remover.");
                }

                $placeholders = implode(',', array_fill(0, count($produtosIds), '?'));

                $sql = "DELETE FROM Itens_Carrinho 
                        WHERE carrinho_id = ? AND produto_id IN ($placeholders)";

                // Junta carrinhoId com os produtosIds em um único array
                $params = array_merge([$carrinhoId], $produtosIds);

                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);

                return true;

            } catch (Exception $e) {
                throw new Exception("Erro ao remover itens do carrinho: " . $e->getMessage());
            }
        }



       public function finalizarPedido($pedidoId, $carrinhoId, $usuarioId, $itensSelecionados) {
            try {
                $this->conn->beginTransaction();

                // Buscar os itens do carrinho
                $placeholders = implode(',', array_fill(0, count($itensSelecionados), '?'));
                $params = array_merge([$usuarioId, $carrinhoId], $itensSelecionados);

                $sql = "
                    SELECT ic.produto_id, ic.quantidade, ic.preco_unitario
                    FROM Itens_Carrinho ic
                    JOIN Carrinhos c ON ic.carrinho_id = c.id
                    WHERE c.usuario_id = ? AND c.id = ? AND ic.produto_id IN ($placeholders)
                ";

                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
                $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$itens) {
                    throw new Exception("Nenhum item encontrado para finalizar o pedido.");
                }

                // Inserir itens no pedido e calcular total
                $insertItem = $this->conn->prepare("
                    INSERT INTO Itens_Pedidos (pedido_id, produto_id, quantidade, preco_unitario)
                    VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)
                ");

                $totalPedido = 0;

                foreach ($itens as $item) {
                    $insertItem->execute([
                        ':pedido_id' => $pedidoId,
                        ':produto_id' => $item['produto_id'],
                        ':quantidade' => $item['quantidade'],
                        ':preco_unitario' => $item['preco_unitario']
                    ]);

                    $totalPedido += $item['quantidade'] * $item['preco_unitario'];
                }

                // Atualizar total do pedido
                $updateTotal = $this->conn->prepare("
                    UPDATE Pedidos SET total = :total WHERE id = :pedido_id
                ");
                $updateTotal->execute([
                    ':total' => $totalPedido,
                    ':pedido_id' => $pedidoId
                ]);

                $this->conn->commit();

                return [
                    'status' => 'success',
                    'message' => 'Pedido criado com sucesso.',
                    'pedido_id' => $pedidoId
                ];

            } catch (Exception $e) {
                $this->conn->rollBack();
                return [
                    'status' => 'error',
                    'message' => 'Erro ao finalizar pedido: ' . $e->getMessage()
                ];
            }
        }


    
}

?>
