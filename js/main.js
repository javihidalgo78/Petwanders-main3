document.addEventListener('DOMContentLoaded', function() {
    // Variables globales para elementos del DOM
    const cartIcon = document.getElementById('cart-icon');
    const cartContainer = document.getElementById('cart-container');
    const closeCart = document.getElementById('close-cart');
    const backdrop = document.getElementById('backdrop');
    const cartItems = document.getElementById('cart-items');
    const cartCount = document.getElementById('cart-count');
    const cartTotal = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');
    const modal = document.getElementById('product-modal');
    const categoryFilter = document.getElementById('category-filter');
    const sortByPrice = document.getElementById('sort-by-price');
    const availabilityFilter = document.getElementById('availability-filter');
    const amazonFilter = document.getElementById('amazon-filter');
    const searchButton = document.getElementById('search-button');
    const container = document.getElementById('productos-container');

    let cart = [];

    // --- Funciones del Carrito ---
    function loadCart() {
        if (sessionStorage.getItem('userLoggedIn')) {
            fetch('get_cart.php')
                .then(response => response.json())
                .then(data => {
                    cart = data;
                    updateCartUI();
                });
        } else {
            const savedCart = localStorage.getItem('petwandersCart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
                updateCartUI();
            }
        }
    }

    function saveCart() {
        if (sessionStorage.getItem('userLoggedIn')) {
            fetch('save_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(cart)
            });
        } else {
            localStorage.setItem('petwandersCart', JSON.stringify(cart));
        }
    }

    function updateCartUI() {
        cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
        cartItems.innerHTML = '';
        if (cart.length === 0) {
            cartItems.innerHTML = '<p>Tu carrito está vacío</p>';
            cartTotal.textContent = 'Total: 0.00€';
            return;
        }
        let total = 0;
        cart.forEach((item, index) => {
            total += item.price * item.quantity;
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <div class="item-details">
                    <div class="item-title">${item.name}</div>
                    <div class="item-price">${item.price.toFixed(2)}€</div>
                    <div class="item-actions">
                        <div class="quantity-control">
                            <button class="quantity-btn decrease" data-index="${index}">-</button>
                            <span class="quantity">${item.quantity}</span>
                            <button class="quantity-btn increase" data-index="${index}">+</button>
                        </div>
                        <button class="remove-item" data-index="${index}"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            `;
            cartItems.appendChild(cartItem);
        });
        cartTotal.textContent = `Total: ${total.toFixed(2)}€`;
        setupCartItemEventListeners();
    }

    function setupCartItemEventListeners() {
        document.querySelectorAll('.quantity-btn.decrease').forEach(btn => {
            btn.addEventListener('click', function() {
                decreaseQuantity(parseInt(this.dataset.index));
            });
        });
        document.querySelectorAll('.quantity-btn.increase').forEach(btn => {
            btn.addEventListener('click', function() {
                increaseQuantity(parseInt(this.dataset.index));
            });
        });
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                removeItem(parseInt(this.dataset.index));
            });
        });
    }

    function addToCart(productId, productName, productPrice, productImage) {
        const existingItemIndex = cart.findIndex(item => item.id === productId);
        if (existingItemIndex !== -1) {
            cart[existingItemIndex].quantity += 1;
        } else {
            cart.push({
                id: productId,
                name: productName,
                price: productPrice,
                image: productImage,
                quantity: 1
            });
        }
        updateCartUI();
        saveCart();
        showCart();
    }

    function increaseQuantity(index) {
        cart[index].quantity += 1;
        updateCartUI();
        saveCart();
    }

    function decreaseQuantity(index) {
        if (cart[index].quantity > 1) {
            cart[index].quantity -= 1;
        } else {
            removeItem(index);
        }
        updateCartUI();
        saveCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        updateCartUI();
        saveCart();
    }

    function showCart() {
        cartContainer.classList.add('active');
        backdrop.classList.add('active');
    }

    function hideCart() {
        cartContainer.classList.remove('active');
        backdrop.classList.remove('active');
    }

    // --- Lógica de Productos y Filtros ---
    function fetchProducts() {
        const category = categoryFilter.value;
        const sortBy = sortByPrice.value;
        const availability = availabilityFilter.checked;
        const amazonOnly = amazonFilter.checked;

        let url = `get_products.php?availability=${availability}&amazonOnly=${amazonOnly}`;
        if (category) {
            url += `&category=${category}`;
        }
        if (sortBy) {
            url += `&sortBy=${sortBy}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                container.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(product => {
                        const card = document.createElement('div');
                        card.className = 'card';
                        card.innerHTML = `
                            <a class="show-product-db" data-product-id="${product.id}" style="cursor: pointer;">
                                <img src="Images/${product.foto}" alt="${product.nombre}">
                            </a>
                            <div>
                                <h3>${product.nombre}</h3>
                                <div class="buy-options">
                                    <button class="add-to-cart-btn" data-product-id="${product.id}" data-product-name="${product.nombre}" data-product-price="${product.precio}" data-product-img="Images/${product.foto}">
                                        Añadir al Carrito - ${parseFloat(product.precio).toFixed(2)}€
                                    </button>
                                </div>
                                <div class="amazon-link">
                                    ${product.amazon_url ? `<a href="${product.amazon_url}" target="_blank"><img src="images/amazon-icon.png" alt="Comprar en Amazon" class="amazon-icon"></a>` : ''}
                                </div>
                            </div>
                        `;
                        container.appendChild(card);
                    });
                } else {
                    container.innerHTML = '<p>No se encontraron productos que coincidan con los filtros.</p>';
                }
            });
    }

    fetch('get_categories.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.nombre;
                option.textContent = category.nombre;
                categoryFilter.appendChild(option);
            });
        });

    // --- Slider ---
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slider .slide');
    if (slides.length > 0) {
        slides[0].classList.add('active');
        function showNextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }
        setInterval(showNextSlide, 8000);
    }

    // --- Event Listeners ---
    cartIcon.addEventListener('click', showCart);
    closeCart.addEventListener('click', hideCart);
    backdrop.addEventListener('click', hideCart);
    searchButton.addEventListener('click', fetchProducts);

    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart-btn')) {
            const button = e.target.closest('.add-to-cart-btn');
            addToCart(button.dataset.productId, button.dataset.productName, parseFloat(button.dataset.productPrice), button.dataset.productImg);
        }

        if (e.target.closest('.show-product-db')) {
            const link = e.target.closest('.show-product-db');
            // Lógica para mostrar el modal del producto
        }
    });

    // Carga inicial
    fetchProducts();
    loadCart();
});