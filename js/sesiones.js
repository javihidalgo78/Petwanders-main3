document.getElementById('cerrarSesion').addEventListener('click', () => {
    const confirmado = confirm("¿Seguro que deseas cerrar la sesión?");
    if (confirmado){
        const cart = JSON.parse(localStorage.getItem('petwandersCart'));
        if (cart) {
            fetch('save_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(cart)
            });
        }
        fetch('logout.php', {
           method: 'POST' 
        })
        .then(() => {
            localStorage.removeItem('petwandersCart');
            window.location.href = 'login.php'
        })
    }
})