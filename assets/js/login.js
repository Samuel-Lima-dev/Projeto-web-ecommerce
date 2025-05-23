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
        console.log(data);

        if (data.status === 'success') {
            
            // Armazena token no localStorage (ou cookies, conforme necessário)
            localStorage.setItem('token', data.token);

            // Redireciona o usuário
            window.location.href = '../../index.html';
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Erro na requisição:', error);
        alert('Erro ao tentar fazer login. Tente novamente.');
    }
});
