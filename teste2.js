const categorias = ['automotivo', 'eletro', 'eletrica'];

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
    titulo.textContent = capitalizar(categoria);
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
        <a href="detalhes.html?id=${produto.id}" class="link-produto">
             <img src="img/${produto.imagem}" alt="${produto.nome}">
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

    // Adiciona o botão "Mais opções"
    const maisOpcoes = document.createElement('div');
    maisOpcoes.classList.add('mais-opcoes');

    maisOpcoes.innerHTML = `<a href="#">Mais opções</a>`;
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

