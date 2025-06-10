<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Produtos</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background-color: #eee;
        }

        a.botao {
            padding: 6px 10px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        a.botao:hover {
            background-color: #2980b9;
        }

        label {
            font-weight: bold;
            margin-top: 20px;
            display: inline-block;
        }

        input#busca-id {
            margin-left: 10px;
            padding: 5px;
            width: 100px;
        }
    </style>
</head>
<body>
    <h1>Lista de Produtos</h1>

    <label for="busca-id">Buscar por ID:
        <input type="number" id="busca-id" placeholder="Digite o ID">
    </label>

    <table id="tabela-produtos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Status</th>
                <th>Categoria</th>
                <th>Fornecedor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- Preenchido dinamicamente -->
        </tbody>
    </table>

    <script>
        let produtos = []; // vai guardar os produtos carregados

        function preencherTabela(lista) {
            const tbody = document.querySelector('#tabela-produtos tbody');
            tbody.innerHTML = ''; // limpa a tabela
            lista.forEach(produto => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${produto.id}</td>
                    <td>${produto.descricao}</td>
                    <td>R$ ${parseFloat(produto.preco).toFixed(2)}</td>
                    <td>${produto.estoque}</td>
                    <td>${produto.status_produto}</td>
                    <td>${produto.categoria_id}</td>
                    <td>${produto.fornecedor_id}</td>
                    <td><a class="botao" href="editar.php?id=${produto.id}">Editar</a></td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Buscar produtos da API e mostrar na tabela
        fetch('http://localhost/ecommerce/api/index.php?controller=produto&action=listar')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    produtos = data.data; // guarda produtos globalmente
                    preencherTabela(produtos);
                } else {
                    alert('Erro ao carregar produtos: ' + data.message);
                }
            })
            .catch(err => {
                alert('Erro de conexão: ' + err);
            });

        // Evento de busca por ID
        document.getElementById('busca-id').addEventListener('input', function() {
            const busca = this.value.trim();
            if (busca === '') {
                // mostra todos se campo estiver vazio
                preencherTabela(produtos);
            } else {
                // filtra pelo ID exatamente igual ao digitado
                const filtro = produtos.filter(p => p.id === Number(busca));
                preencherTabela(filtro);
            }
        });
    </script>
</body>
</html>
