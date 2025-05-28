const categorias = ['UTILIDADES PLASTICOS / CAMEBA', 'eletro', 'eletrica', 'ACES DE BANHEIROS / PIAS E GABINETES', 'ACES. BANHEIROS/CONEXÕES/GABINETES', 'ESQUADRIAS',
    'LUSTRES E LUMINARIAS', 'MÓVEIS E DECORAÇÕES','PIAS E GABINETES','TINTAS E ACESSORIOS','automotivo','ACES. BANHEIROS/GABINETES','BALCAO DE FERRAMENTAS',
    'LOUÇAS METAIS SANIT','UTILIDADES PLASTICOS E CAMEBA'];

const nomesExibicao = {
  'UTILIDADES PLASTICOS / CAMEBA': 'Linha De Banho',
   eletro: 'Eletrodomésticos',
  eletrica: 'Elétrica',
  automotivo: 'Automotivo',
  'ACES DE BANHEIROS / PIAS E GABINETES': 'Acessórios para Banheiro',
  'ACES. BANHEIROS/CONEXÕES/GABINETES': 'Banheiro',
  ESQUADRIAS: 'Ferramentas e Acessórios',
  'LOUÇAS METAIS SANIT': 'Acessórios',
  'LUSTRES E LUMINARIAS': 'Iluminação',
  'MÓVEIS E DECORAÇÕES': 'Móveis e Decoração',
  'PIAS E GABINETES': 'Gabinetes e Pias',
  'TINTAS E ACESSORIOS': 'Tintas','BALCAO DE FERRAMENTAS': 'Ferramenta',
  'ACES. BANHEIROS/GABINETES': 'Utilidades para Banheiro', 'UTILIDADES PLASTICOS E CAMEBA':'Utilidades', 'UTILIDADES DOMÉSTICAS PRESENTES': 'Podutos'
};

document.addEventListener('DOMContentLoaded', () => {
    categorias.forEach(categoria => carregarProdutosPorCategoria(categoria));
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
            renderizarProdutos(categoria, resultado.data);
        } else {
            console.warn(`Nenhum produto encontrado ou dados inválidos para a categoria "${categoria}"`);
        }
    })
    .catch(error => console.error('Erro na requisição:', error));
}
function normalizarCategoria(categoria) {
    return categoria
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "") // Remove acentos
        .replace(/[^a-zA-Z0-9]/g, "") // Remove tudo que não for letra ou número
        .toLowerCase();
}


function renderizarProdutos(categoria, produtos) {
    const categoriaId = `categoria-${normalizarCategoria(categoria)}`;
    const container = document.getElementById(categoriaId);

    if (!container) {
        console.warn(`Container com ID "${categoriaId}" não encontrado no HTML`);
        return;
    }

    container.innerHTML = ''; // Limpa conteúdo anterior

    const titulo = document.createElement('h2');
    titulo.classList.add('titulo-categoria');
    titulo.textContent = nomesExibicao[categoria] || capitalizar(categoria);
    container.appendChild(titulo);

  
    const linhaProdutos = document.createElement('div');
    linhaProdutos.classList.add('linha-produtos');

        //  lista de produtos 
    const lista = document.createElement('div');
    lista.classList.add('lista-produtos');

produtos.slice(0,10).forEach(produto => {
    const divProduto = document.createElement('div');
    divProduto.classList.add('produto');
        

     divProduto.innerHTML = `
        <a href="/ecommerce/pages/accounts/detalhes.html?id=${produto.id}" class="link-produto">
             <img src="assets/img/teste.webp" alt="${produto.nome}">
             <h3>${produto.descricao}</h3>
             <p>R$ ${Number(produto.preco).toFixed(2)}</p>
         </a>
     `;

        lista.appendChild(divProduto);
    });
            // Botão esquerda
    const setaEsquerda = document.createElement('button');
    setaEsquerda.classList.add('seta', 'esquerda');
    setaEsquerda.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24">
        <path d="M14 7l-5 5 5 5V7z"/>
    </svg>
    `;

        // Botão direita
    const setaDireita = document.createElement('button');
    setaDireita.classList.add('seta', 'direita');
    setaDireita.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24">
        <path d="M10 17l5-5-5-5v10z"/>
    </svg>
    `;

    linhaProdutos.appendChild(setaEsquerda); // seta à esquerda
    linhaProdutos.appendChild(lista);        // produtos no meio
    linhaProdutos.appendChild(setaDireita); // seta à direita


    container.appendChild(linhaProdutos);

  const maisOpcoes = document.createElement('div');
    maisOpcoes.classList.add('mais-opcoes');
    maisOpcoes.innerHTML = `<a href="/ecommerce/categoria.html?categoria=${encodeURIComponent(categoria)}">Mais opções</a>`;
    container.appendChild(maisOpcoes);


    setaEsquerda.addEventListener('click', () => {
        lista.scrollLeft -= 300;
    });

    setaDireita.addEventListener('click', () => {
        lista.scrollLeft += 300;
    });



    }

    function capitalizar(texto) {
        return texto.charAt(0).toUpperCase() + texto.slice(1);
    }


