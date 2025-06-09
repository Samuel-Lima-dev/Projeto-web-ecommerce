const nomesExibicao = {
      quarto: 'Quarto',
      cozinha: 'Cozinha',
      sala: 'Sala de Estar',
      banheiro: 'Banheiro',
      automotivo: 'Automotivo',
      eletro: 'Eletrodomésticos',
      eletrica: 'Elétrica',
      'aces de banheiros / pias e gabinetes': 'Acessórios para Banheiro',
      'aces. banheiros/conexões/gabinetes': 'Banheiro',
      esquadrias: 'Ferramentas e Acessórios',
      'louças metais sanit': 'Acessórios',
      'lustres e luminarias': 'Iluminação',
      'móveis e decorações': 'Móveis e Decoração',
      'pias e gabinetes': 'Gabinetes e Pias',
      'tintas e acessorios': 'Tintas'
    };

    document.addEventListener('DOMContentLoaded', () => {
      const params = new URLSearchParams(window.location.search);
      const categoriaParam = params.get('categoria');
      const categoria = categoriaParam ? categoriaParam.toLowerCase() : null;

      if (categoria) {
        const nomeBonito = nomesExibicao[categoria] || categoriaParam;
        document.getElementById('titulo-categoria').textContent = nomeBonito;
        carregarProdutosPorCategoria(categoriaParam);
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
            <img src="assets/img/produtos/${produto.imagem}" alt="Imagem do produto ${produto.nome}">
            <h3>${produto.descricao}</h3>
            <p>R$ ${Number(produto.preco).toFixed(2)}</p>
          </a>
        `;
        lista.appendChild(divProduto);
      });

      container.appendChild(lista);
    }

