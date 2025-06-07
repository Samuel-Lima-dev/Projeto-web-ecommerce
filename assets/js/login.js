document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;

    try {
        const response = await fetch('http://localhost/ecommerce/api/index.php?controller=usuario&action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, senha })
        });

        const data = await response.json();

        if (data.status === 'success') {
            const token = data.token;
            // Armazena o token
            localStorage.setItem('token', token);

            localStorage.setItem('carrinho', data.id_carrinho);
            console.log("Carrinho ID recebido:", data.id_carrinho);
   


            // Redireciona
            window.location.href = '../../index.html';
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Erro na requisição:', error);
        alert('Erro ao tentar fazer login. Tente novamente.');
    }
});
