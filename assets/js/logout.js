function logout(){
    // Remover o token
    localStorage.removeItem('token');
    localStorage.removeItem('carrinho');

    // Redirecionar para a página de login
    window.location.href = 'index.html';

};