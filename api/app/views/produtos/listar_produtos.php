<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de produtos</title>
</head>
<body>

    <h1>Pordutos</h1>
    <a href="index.php?controller=produto&action=cadastrar.php"></a>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($produtos as $produto): ?>
                    <tr>
                        <td><?= htmlspecialchars($produto['id']) ?></td>
                        <td><?= htmlspecialchars($produto['descricao']) ?></td>
                        <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                        <td>
                            <a href="index.php?controller=produto&action=editProduct&id=<?= $produto['id'] ?>">Editar</a>
                            |
                            <a href="index.php?controller=produto&action=excluir&id=<?= $produto['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </table>

    
</body>
</html>