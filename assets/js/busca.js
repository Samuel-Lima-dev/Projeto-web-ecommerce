document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const termoBusca = urlParams.get("buscar");
    

    if (termoBusca) {
        fetch(`http://localhost/ecommerce/api/index.php?controller=produto&action=buscarPorFiltro&descricao=${encodeURIComponent(termoBusca)}`)
            .then(res => res.json())
            .then(dados => {
                const resultadoDiv = document.getElementById("resultados");
                

                if (dados.length > 0) {
                    dados.forEach(produto => {
                        const div = document.createElement("div");
                        div.innerHTML = `<strong>${produto.nome}</strong> - R$ ${produto.preco}`;
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
