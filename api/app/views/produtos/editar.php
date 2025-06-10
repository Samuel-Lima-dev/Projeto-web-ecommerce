
<?php
$id = $_GET['id'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
</head>
<body>
    <h1>Editar Produto</h1>

    <form id="form-editar-produto">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <label>Descrição:<br>
            <input type="text" name="descricao" required>
        </label><br><br>

        <label>Preço:<br>
            <input type="number" name="preco" step="0.01" required>
        </label><br><br>

        <label>Estoque:<br>
            <input type="number" name="estoque" required>
        </label><br><br>

        <label>Status do Produto:<br>
            <input type="text" name="status_produto" required>
        </label><br><br>

        <label>Categoria ID:<br>
            <input type="number" name="categoria_id" required>
        </label><br><br>

        <label>Fornecedor ID:<br>
            <input type="number" name="fornecedor_id" required>
        </label><br><br>

        <label>URL da Imagem:<br>
            <input type="text" name="imagem">
        </label><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>

    <pre id="resultado"></pre>

    <script>
        const id = <?= json_encode($id) ?>;

        // Carrega os dados do produto
        fetch(`http://localhost/ecommerce/api/index.php?controller=produto&action=editarProduto&id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const produto = data.data;
                    for (const key in produto) {
                        const input = document.querySelector(`[name="${key}"]`);
                        if (input) input.value = produto[key];
                    }
                } else {
                    document.getElementById('resultado').textContent = data.message;
                }
            });

        // Submete o formulário com método PUT e JSON puro
        document.getElementById('form-editar-produto').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const dados = {};
            formData.forEach((value, key) => {
                // Converte valores numéricos corretamente
                if (key === 'preco') {
                    dados[key] = parseFloat(value);
                } else if (['estoque', 'id', 'categoria_id', 'fornecedor_id'].includes(key)) {
                    dados[key] = parseInt(value);
                } else {
                    dados[key] = value;
                }
            });

            fetch('http://localhost/ecommerce/api/index.php?controller=produto&action=editarProduto', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dados)
            })
            .then(response => response.json())
            .then(result => {
                document.getElementById('resultado').textContent = JSON.stringify(result, null, 2);
            })
            .catch(error => {
                document.getElementById('resultado').textContent = 'Erro: ' + error;
            });
        });
    </script>
</body>
</html>
