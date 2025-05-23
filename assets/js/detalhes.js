const urlParams = new URLSearchParams(window.location.search);
    const produtoId = urlParams.get('id');

    if (!produtoId) {
        document.getElementById('detalhes-produto').innerHTML = "<h2>ID do produto não informado.</h2>";
    } else {
        fetch(`http://localhost/ecommerce/api/index.php?controller=produto&action=buscarPorId&id=${produtoId}`)
            .then(response => response.json())
            .then(dados => {
                if (dados.status === 'success') {
                    const produto = dados.data;

                    document.getElementById('detalhes-produto').innerHTML = `
                        <img src="/imagens/${produto.imagem}" alt="${produto.nome}">
                        <h1>${produto.descricao}</h1>
                        <p><strong>Preço:</strong> R$ ${Number(produto.preco).toFixed(2)}</p>
                        
                    `;
                } else {
                    document.getElementById('detalhes-produto').innerHTML = "<h2>Produto não encontrado.</h2>";
                }
            })
            .catch(error => {
                console.error(error);
                document.getElementById('detalhes-produto').innerHTML = "<h2>Erro ao carregar os detalhes do produto.</h2>";
            });
    }