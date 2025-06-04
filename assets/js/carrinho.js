// Confere se o usuário está logado, se não, redireciona para tela de login
const token = localStorage.getItem('token');
if (!token) {
    window.location.href = '../pages/accounts/login.html';
}

/*
let listaProdutos = undefined;

buscarProduto = (produtoId) => {
    if (listaProdutos?.length) { // veririfcar se a variável está preenchida e se a lista tem pelo menos 1 item
        return listaProdutos.find(item => item.produto_id == produtoId)
    }
    return undefined
}
*/

qtdMenos = (produto) => {
    let novaQtd = parseInt(produto.quantidade) - 1;
    if (novaQtd <= 0) {
        exclude(produto);
        return false;
    }
    produto.quantidade = novaQtd;
    return true;
}

qtdMais = (produto) => {
    let novaQtd = parseInt(produto.quantidade) + 1;
    const estoque = parseInt(produto.estoque);
    if (estoque < novaQtd) {
        alert('Não há mais itens em estoque');
        return false;
    }
    produto.quantidade = novaQtd;
    return true;
}



// Calcula o Valor total de cada item
precoTotal = (item) => {
    const precoUnitario = parseFloat(item.preco_unitario);
    return precoUnitario * item.quantidade;
}

// Exclui item
exclude = (produtoId) => {
    if (confirm('Deseja excluir o produto?')) {
        const id_carrinho = parseInt(localStorage.getItem('carrinho'));
        const id_produto = parseInt(produtoId);
        fetch('http://localhost/ecommerce/api/index.php?controller=carrinho&action=excluirItem', {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({id_carrinho, id_produto})
        })
        .then(response => response.json())
        .then(data => {if (data.status === 'success') {
            window.location.reload()
        }})}
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

    const carrinhoContainer = document.getElementById('carrinho');
    const finisherContainer = document.getElementById('finisher');
    const categoriasContainer = document.getElementById('categorias');
    const ul = document.createElement('ul');

    // Laço que coloca um li em tela para cada item no carrinho
    if (Array.isArray(data.itens) && data.itens.length > 0) {
        // listaProdutos = data.itens;

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
                    <a class="menos" data-produto="${item.produto_id}">
                        <i class="bx bx-minus"></i>
                    </a>
                    <label>${item.quantidade}</label>
                    <a class="mais" data-produto="${item.produto_id}">
                        <i class="bx bx-plus"></i>
                    </a>
                    <button class="excluirItem" onclick="exclude(${item.produto_id})">Excluir</button>
                </div>
                <label class="preco_total">R$ ${precoTotal(item).toFixed(2)}</label>
            `;

            const ev = new CustomEvent('manipulate', { bubbles: true, detail: item});
            
            const lblPrecoTotal = li.querySelector("label.preco_total");
            const lblQtd = li.querySelector(".quantidade label");
            li.addEventListener('manipulate', (event) => {
                const evItem = event.detail;
                lblQtd.innerHTML = `${evItem.quantidade}`;
                lblPrecoTotal.innerHTML = `R$ ${precoTotal(evItem).toFixed(2)}`;
            });
                
            const aMenos = li.querySelector(".quantidade .menos");
            aMenos.addEventListener('click', () => {
                if (qtdMenos(item.produto_id)) {
                    li.dispatchEvent(ev);
                }
            });

            const aMais = li.querySelector(".quantidade .mais");
            aMais.addEventListener('click', () => {
                if (qtdMais(item.produto_id)) {
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
                    <label class="preco">{Preço Final}</label>
                </div>
                <div class="eco">
                    <label class="texto">Economizou:</label>
                    <label class="preco">{Quantidade Eco}</label>
                </div>
            </div>
            <input type="button" class="finalizar" value="Continuar">
        </div>
        `;

        const finalizar = finisherContainer.getElementsByClassName("finalizar");
        finalizar.disabled = true;
        
        const marker = document.querySelectorAll('.itemCheckbox');
        const markAll = document.getElementById('selectAll')
        const totaltext = document.getElementById('totalItens');

        function atualizarTotal() {
            const totalMarcados = document.querySelectorAll('.itemCheckbox:checked').length;
            totaltext.textContent = totalMarcados;
        }

        marker.forEach(cb => {
            cb.addEventListener('change', atualizarTotal);
        });

        markAll.addEventListener('change', function () {
            const marcar = this.checked;
            checkboxes.forEach(cb => cb.checked = marcar);
            atualizarTotal();
        });
        
        const checkboxes = Array.from(ul.querySelectorAll('.itemCheckbox'));
        const checkAll = finisherContainer.querySelector('#selectAll');

        const btnExcluir = finisherContainer.querySelector('.excluir');
                btnExcluir.addEventListener('click', () => {
                    const paraExcluir = [];
                    const idExcluir = [];
                    const lis = ul.children;
                    for (let i = 0; i < lis.length; i++) {
                        const liElem = lis[i];
                        const checkElem = liElem.querySelector('.itemCheckbox');
                        if (checkElem.checked) {
                            paraExcluir.push(liElem);
                            idExcluir.push(checkElem.getAttribute('data-produto'));
                            console.log(idExclude)
                        }
                    }
                    if (confirm('Deseja excluir os produtos selecionados?')) {
                        paraExcluir.forEach((li, idx) => {
                            const id_carrinho = data.carrinho_id;
                            const id_produto = idExcluir[idx];
                            fetch('http://localhost/ecommerce/api/index.php?controller=carrinho&action=excluirItem', {
                            method: 'delete',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: {id_carrinho, id_produto}
                        })});
                        checkAll.checked = false;
                    } else {
                        return;
                    }});
            
            
            checkAll.addEventListener('change', function(event) {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = checkboxes.every(inputCheck => inputCheck.checked);
                    checkAll.checked = allChecked;
                });
            });

    } else {
        carrinhoContainer.innerHTML += `<p class="aviso_empty">Parece que seu carrinho está vazio. Adicione alguns itens!</p>`;
    }

} else {
    alert(data.message);
}
})
