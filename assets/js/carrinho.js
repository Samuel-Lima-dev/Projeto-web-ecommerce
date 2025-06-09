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

const seguirParaFinalizacao = (produtoId) => {
    
    location.href='finalizacao.html'
}

    // Dropdown do usuário
function exibirNomeUsuario() {
const token = localStorage.getItem('token');

    if (token) {
        try {
            const payload = JSON.parse(atob(token.split('.')[1]));
            const nomeCompleto = payload.user_name;
            const email = payload.email;

            const primeiroNome = nomeCompleto.split(" ")[0];

            // Mostra primeiro nome abaixo do ícone
            document.getElementById('user-firstname').textContent = primeiroNome;

            // Preenche dropdown
            document.getElementById('dropdown-nome').textContent = nomeCompleto;
            document.getElementById('dropdown-email').textContent = email;

            // Troca comportamento do link só se estiver logado
            const loginLink = document.getElementById("login-link");
            loginLink.addEventListener("click", function (e) {
                e.preventDefault(); // impede redirecionamento
                toggleDropdown(e);
            });

        } catch (e) {
            console.error("Token inválido:", e);
            logout(); // força logout
        }
    }
}

function toggleDropdown(event) {
    const menu = document.getElementById("user-dropdown");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user_name');
    localStorage.removeItem('carrinho');
    window.location.reload();
}

exibirNomeUsuario();

// Fecha o menu se clicar fora
document.addEventListener("click", function (e) {
    const dropdown = document.getElementById("user-dropdown");
    const userLink = document.getElementById("login-link");
    if (!userLink.contains(e.target)) {
        dropdown.style.display = "none";
    }
});

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
                <label class="nada1">Nada</label>
                <label class="produto">Produto</label>
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
                    <img src="${item.imagem}" class="img">
                    <label class="nome">${item.descricao}</label>
                    <label class="preco_unitario">R$ ${precoUnitario.toFixed(2)}</label>
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
                    <label class="texto">Total <span id="totalItens">0</span> item(s):</label>
                    <label class="preco">R$ 0,00</label>
            </div>
            <input type="button" class="finalizar" value="Continuar">
        </div>
        `;

        const finalizar = finisherContainer.querySelector(".finalizar");
        finalizar.disabled = true;
        
        // TODO completar o itensSelecionados
        finalizar.addEventListener('click', () => {
            const itensSelecionados = [];
            const lis = ul.children;
            for (let i = 0; i < lis.length; i++) { // Verifica todos os itens de l1
                const checkElem = lis[i].querySelector('.itemCheckbox');
                if (checkElem.checked) {
                    itensSelecionados.push(checkElem.getAttribute('data-produto'));
                }
            }
        })


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
        const lblPrecoTotal = finisherContainer.querySelector('.preco_final .preco');
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
