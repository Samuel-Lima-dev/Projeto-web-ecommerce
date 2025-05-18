document.addEventListener('DOMContentLoaded', () => {
    carregarProdutos();
  });
  
  async function carregarProdutos() {
    const lista = document.getElementById('produtos-lista');
    lista.innerHTML = ''; // Limpa a lista antes de carregar
  
    try {
      const resposta = await fetch('http://localhost/ecommerce/api/index.php?controller=produto&action=listar');
      const resultado = await resposta.json();
  
      if (resultado.status !== 'success') {
        exibirErro('Erro ao carregar produtos: ' + resultado.message);
        return;
      }
  
      resultado.data.forEach(produto => {
        const produtoElemento = criarCardProduto(produto);
        lista.appendChild(produtoElemento);
      });
  
    } catch (erro) {
      exibirErro('Erro na requisição: ' + erro.message);
    }
  }
  
  function criarCardProduto(produto) {
    const container = document.createElement('div');
    container.classList.add('produto');
  
    const nome = criarElementoTexto('strong', produto.descricao);
    const id = criarElementoTexto('p', `ID: ${produto.id}`);
    const preco = criarElementoTexto('p', `Preço: R$ ${parseFloat(produto.preco).toFixed(2)}`);
  
    container.appendChild(nome);
    container.appendChild(id);
    container.appendChild(preco);
  
    return container;
  }
  
  function criarElementoTexto(tag, texto) {
    const elemento = document.createElement(tag);
    elemento.textContent = texto;
    return elemento;
  }
  
  function exibirErro(mensagem) {
    alert(mensagem);
  }
  