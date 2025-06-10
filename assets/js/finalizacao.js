// Confere se o usuário está logado, se não, redireciona para tela de login
const token = localStorage.getItem('token');
if (!token) {
 
    window.location.href = '../pages/accounts/login.html'; // redireciona se não estiver logado
}

const calcularValorTotal = (item) => {
    const precoUnitario = parseFloat(item.preco_unitario);
    return precoUnitario * item.quantidade;
}

const pedido_id = localStorage.getItem("pedido_id");

// Recebe os itens do BD e desenha na tela
fetch('http://localhost/ecommerce/api/index.php?controller=pedido&action=listarItensPedido', {
  method: 'POST',
  headers: {
    "Authorization": `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({pedido_id})
})
.then(response => response.json())
.then(data => {
  if (data.status === "success") {
    console.log("Itens do pedido:", data.itens);

    const contentContainer = document.getElementById('content_container');

    if (Array.isArray(data.itens) && data.itens.length > 0) {

        data.itens.forEach(item => {
            const li = document.createElement('li');

            const imagemPath = `../upload/67f3286446f94.webp`;

            // Convertendo strings em números
            const precoOriginal = parseFloat(item.preco);
            const precoUnitario = parseFloat(item.preco_unitario);

            li.innerHTML = `
            <label class="nome">Nome ${item.descricao}</label>
            <img src="../upload/67f3286446f94.webp" class="img">
            <label class="quantidade">${item.quantidade} x</label>
            <label class="preco_unitario">R$ ${precoUnitario.toFixed(2)}</label>
            <label class="preco_total">R$ ${calcularValorTotal(item).toFixed(2)}</label>
            `;
            
            contentContainer.appendChild(li);
        })
    } else {
        console.error("Erro na resposta da API:", data.message);
    }
}})
.catch(error => {
  console.error("Erro na requisição:", error.message);
});

const sidebarContainer = document.querySelector('.sidebar')
const botoes = Array.from(sidebarContainer.querySelectorAll('.pagamento'));
const finalizar = document.querySelector('.confirmar');
botoes.forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelector('.active')?.classList.remove('active');
        btn.classList.add('active');
        finalizar.disabled = false
        console.log(document.querySelector('.active').id)
    })
});

finalizar.addEventListener('click', () => {
    if (document.querySelector('.active')) {
        const metodoSelec = document.querySelector('.active').id;
        finalizarCompra(pedido_id, metodoSelec);
    }
});

const finalizarCompra = (pedido_id, metodoDP) => {
    const metodo = metodoDP;
    fetch('http://localhost/ecommerce/api/index.php?controller=pedido&action=finalizarPagamento', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
        },
        body: JSON.stringify({pedido_id, metodo})

    })
    .then(response => response.json())
    .then(data => {
    if (data.status === "success") {
        console.log("Compra realizada");
        document.querySelector('.popup').classList.add('show');
        document.querySelector('.popup').removeAttribute('inert');
    } else {
        console.error("Erro ao finalizar compra:", data.message);
    }
    })
    .catch(error => {
    console.error("Erro na requisição:", error);
    });
};

voltarCarinho = () => {
    window.location.href = 'carrinho.html'
}

// Adiciona os itens à tela
/*
li.innerHTML = `
    <label class="nome">Nome ${item.descricao}</label>
    <img src="../upload/67f3286446f94.webp" class="img">
    <label class="quantidade">${item.quantidade} x</label>
    <label class="preco_unitario">R$ ${precoUnitario.toFixed(2)}</label>
    <label class="preco_total">R$ ${calcularValorTotal(item).toFixed(2)}</label>
`;
*/