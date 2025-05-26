function logout(){
    // Remover o token
    localStorage.removeItem('token');

    // Redirecionar para a página de login
    window.location.href = 'index.html';

};