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
                <div class="produto-detalhe">
                    <div class="coluna-esquerda">
                        <img src="/ecommerce/assets/img/teste.webp" alt="Imagem de teste">
                    </div>
                    <div class="coluna-direita">
                        <h1>${produto.descricao}</h1>
                        <p class="preco">R$ ${Number(produto.preco).toFixed(2)}</p>
                        <p class="descricao">
                            ${produto.descricao_completa || 'Descrição não disponível no momento.'}
                        </p>

                        <div class="box-entrega">
                            <p>
                                <img src="/ecommerce/assets/img/icone-retirada.png" alt="Retire na loja" class="icone-entrega1">
                                Retire na loja
                            </p>
                            <p>
                                <img src="/ecommerce/assets/img/caminhão.png" alt="Receba em casa" class="icone-entrega2">
                                Receba em casa
                            </p>
                        </div>


                        <button class="btn-carrinho" onclick="adicionarAoCarrinho(${produto.id}, '${produto.descricao}', ${produto.preco})">
                            Adicionar ao Carrinho
                        </button>

                        <div class="vendido-por">
                            <span>Vendido e entregue por</span>
                            <img src="/ecommerce/assets/img/logo.png" alt="Logo Home Center">
                        </div>
                    </div>
                </div>
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

// Função para adicionar ao carrinho e redirecionar
function adicionarAoCarrinho(id, descricao, preco) {
    const carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];

    const itemExistente = carrinho.find(item => item.id === id);
    if (itemExistente) {
        itemExistente.quantidade += 1;
    } else {
        carrinho.push({ id, descricao, preco, quantidade: 1 });
    }

    localStorage.setItem("carrinho", JSON.stringify(carrinho));

    // Redirecionar para a página do carrinho
    window.location.href = "carrinho.html";
}
