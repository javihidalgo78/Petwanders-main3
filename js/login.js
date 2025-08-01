import { clearCart, loadCartFromData } from './funciones2.js';

document.addEventListener('DOMContentLoaded', function() {
    const loginBtn = document.getElementById('login-btn');
    const logoutBtn = document.getElementById('logout-btn');
    const registerBtn = document.getElementById('register-btn');
    const loginModal = document.getElementById('login-modal');
    const closeLoginModal = document.getElementById('close-login-modal');
    const loginForm = document.getElementById('login-form');

    /**
     * @description Verifica el estado de la sesión del usuario en `sessionStorage`.
     * Muestra u oculta los botones de "Login", "Registro" y "Logout" según si el usuario ha iniciado sesión.
     */
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

    // Event listener para abrir el modal de login al hacer clic en el botón.
    loginBtn.addEventListener('click', () => {
        loginModal.style.display = 'block';
    });

    // Event listener para cerrar el modal de login.
    closeLoginModal.addEventListener('click', () => {
        loginModal.style.display = 'none';
    });

    // Event listener para manejar el envío del formulario de login.
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
                // Si el servidor devuelve un carrito, lo carga en la UI.
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

    // Event listener para el botón de cerrar sesión.
    logoutBtn.addEventListener('click', () => {
        sessionStorage.removeItem('userLoggedIn');
        checkSession();
        clearCart(); // Limpia el carrito de la UI y del almacenamiento local.
        alert('Has cerrado sesión');
    });

    // Verifica el estado de la sesión al cargar la página para asegurar que la UI es correcta.
    checkSession();
});