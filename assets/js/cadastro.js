document.getElementById('form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const nome = document.getElementById('nome').value;
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const cpf = document.getElementById('cpf').value;

    try {
        const response = await fetch('http://localhost/ecommerce/api/index.php?controller=usuario&action=criarConta', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ nome, email, senha, cpf })
        });

        const data = await response.json();

        if (data.status === 'success') {
            alert('Cadastro realizado com sucesso!');
            // Redireciona para a página de login ou principal
            window.location.href = 'login.html';
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Erro ao tentar cadastrar:', error);
        alert('Erro no cadastro. Tente novamente.');
    }
});
