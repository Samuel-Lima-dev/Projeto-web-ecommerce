const token = localStorage.getItem('token');
if (!token) {
    window.location.href = './accounts/login.html'; // redireciona se não estiver logado
}