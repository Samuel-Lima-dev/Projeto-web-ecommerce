// Confere se o usuário está logado, se não, redireciona para tela de login
const token = localStorage.getItem('token');
if (!token) {
 
    window.location.href = '../pages/accounts/login.html'; // redireciona se não estiver logado
}

const sidebarContainer = document.querySelector('.sidebar')
const botoes = Array.from(sidebarContainer.querySelectorAll('.pagamento'));
const finalizar = document.querySelector('.confirmar');
botoes.forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelector('.active')?.classList.remove('active');
        btn.classList.add('active');
        finalizar.disabled = false
    })
});

finalizar.addEventListener('click', () => {
    if (document.querySelector('.active')) {
        const pedidoId = localStorage.getItem("pedido_id");
        const metodoSelec = document.querySelector('.active').id;
        finalizarCompra(pedidoId, metodoSelec);
}});

const finalizarCompra = (pedidoId, metodoDP) => {
    const pedido_id = pedidoId;
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
        console.error("Compra finalizada com sucesso");
    } else {
        console.log("Erro ao finalizar compra:", data.message);
    }
    })
    .catch(error => {
    console.error("Erro na requisição:", error);
    });
};


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