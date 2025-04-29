<h2>Editar Produto</h2>

<form action="index.php?controller=produto&action=editProduct&id=<?php echo $produto['id']; ?>" method="POST">
    <label for="descricao">Descrição:</label><br>
    <input type="text" name="descricao" id="descricao" value="<?php echo htmlspecialchars($produto['descricao']); ?>" required><br><br>

    <label for="preco">Preço:</label><br>
    <input type="number" step="0.01" name="preco" id="preco" value="<?php echo $produto['preco']; ?>" required><br><br>

    <label for="estoque">Estoque:</label><br>
    <input type="number" name="estoque" id="estoque" value="<?php echo $produto['estoque']; ?>" required><br><br>

    <label for="status_produto">Status:</label><br>
    <select name="status_produto" id="status_produto" required>
        <option value="ativo" <?php if($produto['status_produto'] == 'ativo') echo 'selected'; ?>>Ativo</option>
        <option value="inativo" <?php if($produto['status_produto'] == 'inativo') echo 'selected'; ?>>Inativo</option>
    </select><br><br>

    <label for="categoria_id">ID da Categoria:</label><br>
    <input type="number" name="categoria_id" id="categoria_id" value="<?php echo $produto['categoria_id']; ?>" required><br><br>

    <label for="fornecedor_id">ID do Fornecedor:</label><br>
    <input type="number" name="fornecedor_id" id="fornecedor_id" value="<?php echo $produto['fornecedor_id']; ?>" required><br><br>

    <button type="submit">Salvar Alterações</button>
</form>

<a href="index.php?controller=produto&action=listar">Voltar</a>
