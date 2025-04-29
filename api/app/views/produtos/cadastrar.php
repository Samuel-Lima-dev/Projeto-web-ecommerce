<h2>Cadastrar Produto</h2>

<form action="index.php?controller=produto&action=registerProducts" method="POST">
    <label for="descricao">Descrição:</label><br>
    <input type="text" name="descricao" id="descricao" required><br><br>

    <label for="preco">Preço:</label><br>
    <input type="number" step="0.01" name="preco" id="preco" required><br><br>

    <label for="estoque">Estoque:</label><br>
    <input type="number" name="estoque" id="estoque" required><br><br>

    <label for="categoria_id">ID da Categoria:</label><br>
    <input type="number" name="categoria_id" id="categoria_id" required><br><br>

    <label for="fornecedor_id">ID do Fornecedor:</label><br>
    <input type="number" name="fornecedor_id" id="fornecedor_id" required><br><br>

    <button type="submit">Cadastrar</button>
</form>

<a href="index.php?controller=produto&action=listar">Voltar</a>
