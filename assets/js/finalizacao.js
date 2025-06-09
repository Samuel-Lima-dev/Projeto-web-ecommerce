// Confere se o usuário está logado, se não, redireciona para tela de login
const token = localStorage.getItem('token');
if (!token) {
 
    window.location.href = '../pages/accounts/login.html'; // redireciona se não estiver logado
}

const sidebarContainer = document.querySelector('.sidebar')
const botoes = Array.from(sidebarContainer.querySelectorAll('.pagamento'));
botoes.forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelector('.active')?.classList.remove('active');
        btn.classList.add('active');
        document.querySelector('.confirmar').disabled = false
    })
})


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