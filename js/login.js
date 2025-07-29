
import { clearCart, loadCartFromData } from './funciones2.js';

document.addEventListener('DOMContentLoaded', function() {
    const loginBtn = document.getElementById('login-btn');
    const logoutBtn = document.getElementById('logout-btn');
    const registerBtn = document.getElementById('register-btn');
    const loginModal = document.getElementById('login-modal');
    const closeLoginModal = document.getElementById('close-login-modal');
    const loginForm = document.getElementById('login-form');

    // Función para verificar el estado de la sesión
    function checkSession() {
        const userLoggedIn = sessionStorage.getItem('userLoggedIn');
        if (userLoggedIn) {
            loginBtn.style.display = 'none';
            registerBtn.style.display = 'none';
            logoutBtn.style.display = 'block';
        } else {
            loginBtn.style.display = 'block';
            registerBtn.style.display = 'block';
            logoutBtn.style.display = 'none';
        }
    }

    // Abrir el modal de login
    loginBtn.addEventListener('click', () => {
        loginModal.style.display = 'block';
    });

    // Cerrar el modal de login
    closeLoginModal.addEventListener('click', () => {
        loginModal.style.display = 'none';
    });

    // Enviar el formulario de login
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                sessionStorage.setItem('userLoggedIn', true);
                checkSession();
                loginModal.style.display = 'none';
                alert('Inicio de sesión exitoso');
                if (data.cart) {
                    localStorage.setItem('petwandersCart', JSON.stringify(data.cart));
                    loadCartFromData(data.cart);
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Cerrar sesión
    logoutBtn.addEventListener('click', () => {
        sessionStorage.removeItem('userLoggedIn');
        checkSession();
        clearCart();
        alert('Has cerrado sesión');
    });

    // Verificar el estado de la sesión al cargar la página
    checkSession();
});
