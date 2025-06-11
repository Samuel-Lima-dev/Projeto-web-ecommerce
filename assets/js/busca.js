document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const termoBusca = urlParams.get("buscar");

    if (termoBusca) {
        fetch("http://localhost/ecommerce/api/index.php?controller=produto&action=buscarPorFiltro", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ descricao: termoBusca })
        })
        .then(res => res.json())
        .then(dados => {
            const resultadoDiv = document.getElementById("resultados");
            const produtos = dados.data;

            if (produtos && produtos.length > 0) {
                produtos.forEach(produto => {
                    const div = document.createElement("div");
                    div.innerHTML = `
                    <a href="../accounts/detalhes.html?id=${produto.id}" class="link-produto">
                        <img src="${produto.caminho_imagem}" alt="${produto.nome}">
                        <h3>${produto.descricao}</h3>
                        <p>R$ ${Number(produto.preco).toFixed(2)}</p>
                    </a>
                    `;
                    resultadoDiv.appendChild(div);
                });
            } else {
                resultadoDiv.textContent = "Nenhum resultado encontrado.";
            }
        })

        .catch(erro => {
            console.error("Erro na busca:", erro);
            document.getElementById("resultados").textContent = "Erro ao buscar produtos.";
        });
    }
});

