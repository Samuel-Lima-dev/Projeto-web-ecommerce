  
                const nomesExibicao = {
        automotivo: 'Automotivo',
        eletro: 'Eletrodomésticos',
        eletrica: 'Elétrica',
        'ACES DE BANHEIROS / PIAS E GABINETES': 'Acessórios para Banheiro',
        'ACES. BANHEIROS/CONEXÕES/GABINETES': 'Banheiro',
        ESQUADRIAS: 'Ferramentas e Acessórios',
        'LOUÇAS METAIS SANIT': 'Acessórios',
        'LUSTRES E LUMINARIAS': 'Iluminação',
        'MÓVEIS E DECORAÇÕES': 'Móveis e Decoração',
        'PIAS E GABINETES': 'Gabinetes e Pias',
        'TINTAS E ACESSORIOS': 'Tintas'
        };

        document.addEventListener('DOMContentLoaded', () => {
                const params = new URLSearchParams(window.location.search);
                const categoria = params.get('categoria');

                if (categoria) {
                    const nomeBonito = nomesExibicao[categoria] || categoria;
                    document.getElementById('titulo-categoria').textContent = nomeBonito;
                    console.log("Categoria recebida na URL:", categoria);
                    carregarProdutosPorCategoria(categoria);
                } else {
                    document.getElementById('titulo-categoria').textContent = "Categoria não especificada.";
                }
            });
        function carregarProdutosPorCategoria(categoria) {
            fetch('http://localhost/ecommerce/api/index.php?controller=produto&action=buscarPorFiltro', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    categoria: categoria,
                    descricao: '',
                    fornecedor: ''
                })
            })
            .then(response => response.json())
            .then(resultado => {
                if (resultado.status === 'success' && Array.isArray(resultado.data)) {
                 renderizarTodosProdutos(resultado.data);
                } else {
                    document.getElementById('container-categoria').innerHTML = '<p>Nenhum produto encontrado.</p>';
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                document.getElementById('container-categoria').innerHTML = '<p>Erro ao carregar produtos.</p>';
            });
        }
        

        function renderizarTodosProdutos(produtos) {
            const container = document.getElementById('container-categoria');
            container.innerHTML = '';

            const lista = document.createElement('div');
            lista.classList.add('lista-produtos');

            produtos.forEach(produto => {
                const divProduto = document.createElement('div');
                divProduto.classList.add('produto');
                divProduto.innerHTML = `
                    <a href="pages/accounts/detalhes.html?id=${produto.id}" class="link-produto">
                        <img src="img/${produto.imagem}" alt="${produto.nome}">
                        <h3>${produto.descricao}</h3>
                        <p>R$ ${Number(produto.preco).toFixed(2)}</p>
                    </a>
                `;
                lista.appendChild(divProduto);
            });

            container.appendChild(lista);
        }
    