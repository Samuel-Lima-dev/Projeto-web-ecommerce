const token = localStorage.getItem('token');
if (!token) {
<<<<<<< Updated upstream
=======
 HEAD
>>>>>>> Stashed changes
    window.location.href = '../pages/accounts/login.html'; // redireciona se não estiver logado
}

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

    const ul = document.createElement('ul');

    if (Array.isArray(data.itens) && data.itens.length > 0) {
        data.itens.forEach(item => {
            const li = document.createElement('li');
            li.classList.add('carrinho_item');

            const imagemPath = `../upload/67f3286446f94.webp`;

            // Convertendo strings em números
            const precoOriginal = parseFloat(item.preco);
            const precoUnitario = parseFloat(item.preco_unitario);
            const precoTotal = precoUnitario * item.quantidade;

            li.innerHTML = `
                <label class="marker"> 
                    <input type="checkbox">
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
                    <a href="#" class="menos" data-produto="${item.produto_id}">
                        <i class="bx bx-minus"></i>
                    </a>
                    <label>${item.quantidade}</label>
                    <a href="#" class="mais" data-produto="${item.produto_id}">
                        <i class="bx bx-plus"></i>
                    </a>
                </div>
                <label class="preco_total">R$ ${precoTotal.toFixed(2)}</label>
            `;

            ul.appendChild(li);
        });

        carrinhoContainer.appendChild(ul);
    } else {
        carrinhoContainer.innerHTML += `<p>Carrinho vazio.</p>`;
    }

} else {
    alert(data.message);
}
})
