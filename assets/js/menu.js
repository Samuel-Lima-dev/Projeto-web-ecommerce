 // Login e bt de sair

document.addEventListener('DOMContentLoaded', () => {
    const nome = localStorage.getItem('nome');
    if (nome) {
        const userInfoDiv = document.getElementById('user-info');
        userInfoDiv.textContent = `Olá, ${nome}!`;
    }
});
 function exibirNomeUsuario() {
    const token = localStorage.getItem('token');

    if (token) {
        try {
            const payload = JSON.parse(atob(token.split('.')[1]));
            const nomeCompleto = payload.user_name;
            const email = payload.email;

            const primeiroNome = nomeCompleto.split(" ")[0];

            // Mostra primeiro nome abaixo do ícone
            document.getElementById('user-firstname').textContent = primeiroNome;

            // Preenche dropdown
            document.getElementById('dropdown-nome').textContent = nomeCompleto;
            document.getElementById('dropdown-email').textContent = email;

            // Troca comportamento do link só se estiver logado
            const loginLink = document.getElementById("login-link");
            loginLink.addEventListener("click", function (e) {
                e.preventDefault(); // impede redirecionamento
                toggleDropdown(e);
            });

        } catch (e) {
            console.error("Token inválido:", e);
            logout(); // força logout
        }
    }
}

function toggleDropdown(event) {
    const menu = document.getElementById("user-dropdown");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user_name');
    localStorage.removeItem('carrinho');
    window.location.reload();
}

exibirNomeUsuario();

// Fecha o menu se clicar fora
document.addEventListener("click", function (e) {
    const dropdown = document.getElementById("user-dropdown");
    const userLink = document.getElementById("login-link");
    if (!userLink.contains(e.target)) {
        dropdown.style.display = "none";
    }
});

