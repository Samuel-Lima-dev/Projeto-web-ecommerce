// Confere se o usuário está logado, se não, redireciona para tela de login
const token = localStorage.getItem('token');
if (!token) {
 
    window.location.href = '../pages/accounts/login.html'; // redireciona se não estiver logado
}
// Guarda lista de produtos do recebida do fetch
let listaProdutos = undefined;

buscarProduto = (produtoId) => {
    if (listaProdutos?.length) { // veririfcar se a variável está preenchida e se a lista tem pelo menos 1 item
        return listaProdutos.find(item => item.produto_id == produtoId)
    }
    return undefined
}


const reduzirQuantidade = (produto) => {
    let novaQtd = parseInt(produto.quantidade) - 1;
    if (novaQtd <= 0) {
        confirmarExcluir(produto.produto_id);
        return false;
    }
    produto.quantidade = novaQtd;
    return true;
}

const acrecentarQuantidade = (produto) => {
    let novaQtd = parseInt(produto.quantidade) + 1;
    const estoque = parseInt(produto.estoque);
    if (estoque < novaQtd) {
        alert('Não há mais itens em estoque');
        return false;
    }
    produto.quantidade = novaQtd;
    return true;
}

const calcularValorTotal = (item) => {
    const precoUnitario = parseFloat(item.preco_unitario);
    return precoUnitario * item.quantidade;
}

const confirmarExcluir = (produtoId) => {
    if (confirm('Deseja excluir o produto?')) {
        excluir(produtoId) 
        .then(data => {if (data.status === 'success') {
            window.location.reload()
        }});
    }
};

const excluir = (produtoId) => {
    const id_produto = parseInt(produtoId);
    return fetch('http://localhost/ecommerce/api/index.php?controller=carrinho&action=excluirItem', {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({id_produto})
    })
    .then(response => response.json());
};


// Requisição para pegar os itens no carrinho
fetch('http://localhost/ecommerce/api/index.php?controller=carrinho&action=listarItemCarrinho', {
    method: 'GET',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
if (data.status === 'success') {

    const categoriasContainer = document.getElementById('categorias');
    const carrinhoContainer = document.getElementById('carrinho');
    const finisherContainer = document.getElementById('finisher');
    const ul = document.createElement('ul');

    // Laço que coloca um li em tela para cada item no carrinho
    if (Array.isArray(data.itens) && data.itens.length > 0) {
        listaProdutos = data.itens;

        data.itens.forEach(item => {
            const li = document.createElement('li');
            li.classList.add('carrinho_item');

            const imagemPath = `../upload/67f3286446f94.webp`;

            // Convertendo strings em números
            const precoOriginal = parseFloat(item.preco);
            const precoUnitario = parseFloat(item.preco_unitario);

            // Adiciona as categorias à tela
            categoriasContainer.innerHTML = `
            <div class="lista_categorias">
                <label class="produto">Produtos</label>
                <label class="preco-unitario">Preço Unitário</label>
                <label class="quantidade">Quantidade</label>
                <label class="preco-total">Preço total</label>
            </div>
            `;
            
            // Adiciona os itens à tela
            li.innerHTML = `
                <label class="marker"> 
                    <input type="checkbox" class="itemCheckbox" data-produto="${item.produto_id}">
                </label>
                <div class="imagem">
                    <img src="${imagemPath}" class="img">
                    <label class="promo_desc">${item.status_produto === 'promo' ? 'Promoção' : ''}</label>
                </div>
                <div class="item_name">
                    <label class="descricao">${item.descricao}</label>
                </div>
                <div class="preco_uni">
                    <label class="preco_promo">R$ ${precoUnitario.toFixed(2)}</label>
                </div>
                <div class="quantidade">
                    <button class="excluirItem" onclick="confirmarExcluir(${item.produto_id})">Excluir</button>
                    <a class="menos" data-produto="${item.produto_id}">
                        <i class="bx bx-minus"></i>
                    </a>
                    <label>${item.quantidade}</label>
                    <a class="mais" data-produto="${item.produto_id}">
                        <i class="bx bx-plus"></i>
                    </a>
                </div>
                <label class="preco_total">R$ ${calcularValorTotal(item).toFixed(2)}</label>
            `;

            // Evento de manipulação  quantidade do item
            const ev = new CustomEvent('manipulate', { bubbles: true, detail: item});
            
            const lblPrecoTotal = li.querySelector("label.preco_total");
            const lblQtd = li.querySelector(".quantidade label");
            li.addEventListener('manipulate', (event) => {
                const evItem = event.detail;
                lblQtd.innerHTML = `${evItem.quantidade}`;
                lblPrecoTotal.innerHTML = `R$ ${calcularValorTotal(evItem).toFixed(2)}`;
                atualizarSelect();
            });

            const aMenos = li.querySelector(".quantidade .menos");
            aMenos.addEventListener('click', () => {
                if (reduzirQuantidade(item)) {
                    li.dispatchEvent(ev);
                }
            });

            const aMais = li.querySelector(".quantidade .mais");
            aMais.addEventListener('click', () => {
                if (acrecentarQuantidade(item)) {
                    li.dispatchEvent(ev);
                }
            });
           
            ul.appendChild(li);
        });

        carrinhoContainer.appendChild(ul);

        // Adiciona a finalização à tela
        finisherContainer.innerHTML = `
        <div class="finisher">
            <label class="select_all" for="selectAll"> 
                <input type="checkbox" id="selectAll">
                Selecionar Tudo (${data.itens.length})
            </label>
            <input type="button" class="excluir" value="Excluir">
            <div class="preco_final">
                <div class="total">
                    <label class="texto">Total (<span id="totalItens">0</span> itens):</label>
                    <label class="preco">R$ 0,00</label>
                </div>
            </div>
            <input type="button" class="finalizar" value="Continuar">
        </div>
        `;

        // Desabilita o botão finalizar
        const finalizar = finisherContainer.querySelector(".finalizar");
        finalizar.disabled = true;
        

        const btnExcluir = finisherContainer.querySelector('.excluir');
        btnExcluir.addEventListener('click', () => {
            const idExcluir = [];
            const lis = ul.children;
            for (let i = 0; i < lis.length; i++) { // Verifica todos os itens de l1
                const checkElem = lis[i].querySelector('.itemCheckbox');
                if (checkElem.checked) {
                    idExcluir.push(checkElem.getAttribute('data-produto'));
                }
            }
            if (idExcluir.length && confirm('Deseja excluir o(s) produto(s) selecionado(s)?')) {
                idExcluir.forEach(id_produto => excluir(id_produto));
                window.location.reload();
            } else {
                return;
            }
        });
            

        // Controle de checkboxes e totalizadores
        const checkboxes = Array.from(ul.querySelectorAll('.itemCheckbox'));
        const checkAll = finisherContainer.querySelector('#selectAll');

        checkAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                atualizarSelect();
            });
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = checkboxes.every(inputCheck => inputCheck.checked);
                checkAll.checked = allChecked;
                atualizarSelect();
            });
        });

        
        const spanQuantidadeSelecionado = finisherContainer.querySelector('#totalItens');
        const lblPrecoTotal = finisherContainer.querySelector('.total .preco');
        atualizarSelect = () => {
            const itensMarcados = Array.from(ul.querySelectorAll('.itemCheckbox:checked'));

            const ValorTotal = itensMarcados
                .map(checkElem => buscarProduto(checkElem.getAttribute('data-produto')))
                .reduce((total, produto) => total + calcularValorTotal(produto), 0)

            const totalMarcados = itensMarcados.length;
            spanQuantidadeSelecionado.textContent = totalMarcados;
            lblPrecoTotal.textContent = 'R$ ' + ValorTotal.toFixed(2);
            finalizar.disabled = !Boolean(totalMarcados);
        }


    } else {
        carrinhoContainer.innerHTML += `<p class="aviso_empty">Parece que seu carrinho está vazio. Adicione alguns itens!</p>`;
    }

} else {
    alert(data.message);
}
})
