const token = localStorage.getItem('token');
if (!token) {
    window.location.href = 'login.html'; // redireciona se não estiver logado
}