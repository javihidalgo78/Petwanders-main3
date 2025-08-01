import { productos } from './productos.js';

// PASO 1: Declarar variables globales para elementos DOM
let cartIcon, cartContainer, closeCart, backdrop, cartItems, cartCount, cartTotal, checkoutBtn;
let addToCartButtons, modal;

// PASO 2: Definir el carrito como variable global
let cart = [];

// PASO 3: Definir todas las funciones del carrito a nivel global

/**
 * Carga el carrito de compras.
 * Si el usuario ha iniciado sesión (verificado con sessionStorage), carga el carrito desde el servidor a través de 'get_cart.php'.
 * De lo contrario, intenta cargarlo desde el localStorage del navegador.
 * Después de cargar, actualiza la interfaz de usuario del carrito.
 */
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

/**
 * Guarda el estado actual del carrito.
 * Si el usuario ha iniciado sesión, envía los datos del carrito al servidor ('save_cart.php') mediante una solicitud POST.
 * Si no, guarda el carrito en el localStorage del navegador.
 */
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

/**
 * Actualiza la interfaz de usuario del carrito de compras.
 * Muestra el número total de artículos, renderiza cada artículo en el carrito con sus detalles
 * y botones de acción (aumentar, disminuir, eliminar).
 * Calcula y muestra el precio total. Si el carrito está vacío, muestra un mensaje.
 */
function updateCartUI() {
  // Actualizar contador
  cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
  
  // Limpiar carrito actual
  cartItems.innerHTML = '';
  
  if (cart.length === 0) {
    cartItems.innerHTML = '<p>Tu carrito está vacío</p>';
    cartTotal.textContent = 'Total: 0.00€';
    return;
  }
  
  // Calcular total
  let total = 0;
  
  // Añadir elementos al carrito
  cart.forEach((item, index) => {
    total += item.price * item.quantity;
    
    const cartItem = document.createElement('div');
    cartItem.className = 'cart-item';
    cartItem.innerHTML = `
      <img src="${item.image}" alt="${item.name}">
      <div class="item-details">
        <div class="item-title">${item.name}</div>
        <div class="item-size">Talla: ${item.size}</div>
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
  
  // Actualizar total
  cartTotal.textContent = `Total: ${total.toFixed(2)}€`;
  
  // Añadir controladores de eventos para los botones del carrito
  setupCartItemEventListeners();
}

/**
 * Configura los event listeners para los botones de control de cantidad y eliminación
 * dentro de cada artículo del carrito.
 */
function setupCartItemEventListeners() {
  document.querySelectorAll('.quantity-btn.decrease').forEach(btn => {
    btn.addEventListener('click', function() {
      const index = parseInt(this.dataset.index);
      decreaseQuantity(index);
    });
  });
  
  document.querySelectorAll('.quantity-btn.increase').forEach(btn => {
    btn.addEventListener('click', function() {
      const index = parseInt(this.dataset.index);
      increaseQuantity(index);
    });
  });
  
  document.querySelectorAll('.remove-item').forEach(btn => {
    btn.addEventListener('click', function() {
      const index = parseInt(this.dataset.index);
      removeItem(index);
    });
  });
}

/**
 * Añade un producto al carrito.
 * Si un producto con el mismo ID y talla ya existe, incrementa su cantidad.
 * De lo contrario, añade el producto como un nuevo artículo.
 * Finalmente, actualiza la UI, guarda el carrito y lo muestra.
 * @param {string} productId - El ID del producto.
 * @param {string} productName - El nombre del producto.
 * @param {number} productPrice - El precio del producto.
 * @param {string} productImage - La URL de la imagen del producto.
 * @param {string} productSize - La talla u opción seleccionada para el producto.
 */
function addToCart(productId, productName, productPrice, productImage, productSize) {
  // Buscar si el producto ya está en el carrito con la misma talla
  const existingItemIndex = cart.findIndex(item => 
    item.id === productId && item.size === productSize
  );
  
  if (existingItemIndex !== -1) {
    // Incrementar cantidad si ya existe el mismo producto con la misma talla
    cart[existingItemIndex].quantity += 1;
  } else {
    // Añadir nuevo producto
    cart.push({
      id: productId,
      name: productName,
      price: productPrice,
      image: productImage,
      size: productSize,
      quantity: 1
    });
  }
  
  // Actualizar UI y guardar
  updateCartUI();
  saveCart();
  
  // Mostrar carrito
  showCart();
}

/**
 * Aumenta la cantidad de un artículo en el carrito.
 * @param {number} index - El índice del artículo en el array del carrito.
 */
function increaseQuantity(index) {
  cart[index].quantity += 1;
  updateCartUI();
  saveCart();
}

/**
 * Disminuye la cantidad de un artículo en el carrito.
 * Si la cantidad es mayor que 1, la reduce. Si es 1, elimina el artículo del carrito.
 * @param {number} index - El índice del artículo en el array del carrito.
 */
function decreaseQuantity(index) {
  if (cart[index].quantity > 1) {
    cart[index].quantity -= 1;
  } else {
    removeItem(index);
  }
  updateCartUI();
  saveCart();
}

/**
 * Elimina un artículo del carrito por su índice.
 * @param {number} index - El índice del artículo a eliminar.
 */
function removeItem(index) {
  cart.splice(index, 1);
  updateCartUI();
  saveCart();
}

/**
 * Vacía completamente el carrito de compras.
 * @export
 */
export function clearCart() {
  cart = [];
  updateCartUI();
  saveCart();
}

/**
 * Carga datos de un carrito desde una fuente externa y actualiza la UI.
 * @param {Array} cartData - Un array de objetos de artículos del carrito.
 * @export
 */
export function loadCartFromData(cartData) {
    cart = cartData;
    updateCartUI();
}

/**
 * Muestra el contenedor del carrito y el fondo oscuro.
 */
function showCart() {
  cartContainer.classList.add('active');
  backdrop.classList.add('active');
}

/**
 * Oculta el contenedor del carrito y el fondo oscuro.
 */
function hideCart() {
  cartContainer.classList.remove('active');
  backdrop.classList.remove('active');
}

/**
 * Convierte una cadena de texto de características (separadas por saltos de línea y un punto) en una lista HTML `<ul>`.
 * @param {string} featuresTexto - El texto con las características del producto.
 * @returns {string} Una cadena HTML con la lista de características.
 */
function convertirFeaturesALista(featuresTexto) {
  return `<ul class="features">${
    featuresTexto
      .split(/\r?\n/)
      .filter(linea => linea.trim().startsWith("•"))
      .map(linea => `<li>${linea.replace(/^•\s*/, "")}</li>`)
      .join("")
  }</ul>`;
}

/**
 * Genera el código HTML para mostrar un producto.
 * Puede generar una vista de tarjeta estándar o una vista para un modal.
 * @param {object} producto - El objeto del producto con sus detalles.
 * @param {boolean} modal - Si es `true`, genera el HTML para la vista de modal.
 * @returns {string} El código HTML de la tarjeta del producto.
 */
function displayProduct(producto, modal) {
  let opciones = [];
  let tipoSelect = "";
  
  if (producto.tallas) {
    opciones = producto.tallas;
    tipoSelect = "talla";
  } else if (producto.capacidades) {
    opciones = producto.capacidades;
    tipoSelect = "capacidad";
  } else if (producto.colores) {
    opciones = producto.colores;
    tipoSelect = "color";
  }
  
  const opcionesHTML = opciones.map(opcion =>
    `<option value="${opcion}">${opcion}</option>`
  ).join("");

  const amazonUrl = producto.amazonUrl || "https://www.amazon.com/s?k=" + encodeURIComponent(producto.nombre);
  
  const descripcion = modal ? '' : `${producto.descripcion}`;
  const features = modal ? convertirFeaturesALista(producto.features) : "";
  
  const selectId = modal ? `modal-size-select-${producto.id}` : `size-select-${producto.id}`;
  const buttonClass = modal ? "modal-add-to-cart-btn" : "add-to-cart-btn";
  
  const cardHTML = `
    <div class="card">
      ${modal ? '' : `
      <a 
        data-product-id="${producto.id}"
        data-product-name="${producto.nombre}"
        data-product-price="${producto.precio}"
        data-product-img="${producto.imagen}"
        data-product-description="${producto.descripcion}"
        class="show-product">
        <img src="${producto.imagen}" alt="${producto.nombre}">
      </a>
      `}
      
      ${modal ? `<img src="${producto.imagen}" alt="${producto.nombre}" style="max-width: 100%;">` : ''}
      
      <div>
        <h3>${producto.nombre}</h3>
        <p>${descripcion}</p>
        ${features}
        <div class="buy-options">
          <select id="${selectId}" class="size-select" data-product-id="${producto.id}">
            <option value="">Seleccionar ${tipoSelect}</option>
            ${opcionesHTML}
          </select>
          <button
            class="${buttonClass}"
            data-product-id="${producto.id}"
            data-product-name="${producto.nombre}"
            data-product-price="${producto.precio}"
            data-product-img="${producto.imagen}">
            Añadir al Carrito - ${producto.precio.toFixed(2)}€
          </button>
        </div>
        <div class="amazon-link">
          <a href="${amazonUrl}" target="_blank" rel="noopener noreferrer">
            <img src="images/amazon-icon.png" alt="Comprar en Amazon" title="Comprar en Amazon" class="amazon-icon">
          </a>
        </div>
      </div>
    </div>
  `;

  return cardHTML;
}

// PASO 4: Poner la inicialización y event listeners dentro de DOMContentLoaded
// En funciones2.js, dentro del evento DOMContentLoaded
const contenedor = document.getElementById('productos-container');
if (contenedor && contenedor.children.length === 0) {
  // Solo muestra los productos si el contenedor está vacío
  productos.forEach(producto => {
    contenedor.innerHTML += displayProduct(producto, false);
  });
}
document.addEventListener('DOMContentLoaded', function() {
  // Inicializar las referencias a elementos DOM
  modal = document.getElementById('product-modal');
  cartIcon = document.getElementById('cart-icon');
  cartContainer = document.getElementById('cart-container');
  closeCart = document.getElementById('close-cart');
  backdrop = document.getElementById('backdrop');
  cartItems = document.getElementById('cart-items');
  cartCount = document.getElementById('cart-count');
  cartTotal = document.getElementById('cart-total');
  checkoutBtn = document.getElementById('checkout-btn');
  
  // Slider
  const sliderSetup = function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slider .slide');
    
    function showNextSlide() {
      slides[currentSlide].classList.remove('active');
      currentSlide = (currentSlide + 1) % slides.length;
      slides[currentSlide].classList.add('active');
    }
    
    setInterval(showNextSlide, 8000); // Cambia cada 8 segundos
  };
  
  // Iniciar el slider si hay slides
  if (document.querySelectorAll('.slider .slide').length > 0) {
    sliderSetup();
  }
  
  // Configurar eventos del carrito
  cartIcon.addEventListener('click', showCart);
  closeCart.addEventListener('click', hideCart);
  backdrop.addEventListener('click', hideCart);
  
  // Configurar evento para finalizar compra
  checkoutBtn.addEventListener('click', function() {
    if (cart.length === 0) {
      alert('Tu carrito está vacío');
      return;
    }
    
    alert('¡Gracias por tu compra! Procesando pedido...');
    cart = [];
    updateCartUI();
    saveCart();
    hideCart();
  });
  
  // Cargar categorías y productos
  const categoryFilter = document.getElementById('category-filter');
  const sortByPrice = document.getElementById('sort-by-price');
  const availabilityFilter = document.getElementById('availability-filter');
  const amazonFilter = document.getElementById('amazon-filter');

  /**
   * Obtiene los productos del servidor basándose en los filtros seleccionados
   * y los muestra en la página.
   */
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
        const container = document.getElementById('productos-container');
        container.innerHTML = ''; // Limpiar contenedor
        data.forEach(product => {
          console.log(product)
          const card = document.createElement('div');
          card.className = 'card';
          card.innerHTML = `
            <a class="show-product-db" data-product-id="${product.id}" data-product-name="${product.nombre}" data-product-price="${product.precio}" data-product-img="Images/${product.foto}" style="cursor: pointer;">
              <img src="Images/${product.foto}" alt="${product.nombre}">
            </a>
            <div>
              <h3>${product.nombre}</h3>
              <div class="buy-options">
                <button
                  class="add-to-cart-btn"
                  data-product-id="${product.id}"
                  data-product-name="${product.nombre}"
                  data-product-price="${product.precio}"
                  data-product-img="Images/${product.foto}">
                  Añadir al Carrito - ${parseFloat(product.precio).toFixed(2)}€
                </button>
              </div>
              <div class="amazon-link">
                ${product.amazonUrl ? `<a href="${product.amazonUrl}" target="_blank"><img src="images/amazon-icon.png" alt="Comprar en Amazon" class="amazon-icon"></a>` : ''}
              </div>
            </div>
          `;
          container.appendChild(card);
        });
      });
  }

  // Obtiene y popula las categorías de productos en el filtro.
  fetch('get_categories.php')
    .then(response => response.json())
    .then(data => {
      data.forEach(category => {
        const option = document.createElement('option');
        option.value = category;
        option.textContent = category;
        categoryFilter.appendChild(option);
      });
    });

  const searchButton = document.getElementById('search-button');

  searchButton.addEventListener('click', fetchProducts);

  // Carga inicial de productos
  fetchProducts();

  // Usa delegación de eventos para manejar los clics en "Añadir al carrito".
  document.addEventListener('click', function(e) {
    const button = e.target.closest('.add-to-cart-btn');
    if (!button) return;

    const productId = button.dataset.productId;
    const productName = button.dataset.productName;
    const productPrice = parseFloat(button.dataset.productPrice);
    const productImage = button.dataset.productImg;

    const context = button.closest('.card, .modal-content');
    let productSize = 'N/A'; 

    if (context) {
      const sizeSelect = context.querySelector('.size-select');
      if (sizeSelect) {
        productSize = sizeSelect.value;
        if (!productSize) {
          alert('Por favor, selecciona una opción');
          return; 
        }
      }
    }
    
    addToCart(productId, productName, productPrice, productImage, productSize);

    if (button.closest('.modal-content')) {
      const modal = document.getElementById('product-modal');
      if (modal) {
        modal.style.display = 'none';
      }
    }
  });
  
  // Usa delegación de eventos para mostrar el modal de detalles del producto.
  document.addEventListener('click', function(e) {
    const showProductLink = e.target.closest('.show-product, .show-product-db');
    if (showProductLink) {
      const productId = showProductLink.dataset.productId;
      const productName = showProductLink.dataset.productName;
      const productPrice = parseFloat(showProductLink.dataset.productPrice);
      const productImage = showProductLink.dataset.productImg;
      const productDescription = showProductLink.dataset.productDescription || 'Descripción no disponible.';

      const modalContent = document.getElementById('modal-content');
      if (!modalContent) return;

      modalContent.innerHTML = `
        <span class="close-modal">&times;</span>
        <img src="${productImage}" alt="${productName}" style="max-width: 100%;">
        <h3>${productName}</h3>
        <p>${productDescription}</p>
        <div class="buy-options">
          <button
            class="modal-add-to-cart-btn add-to-cart-btn"
            data-product-id="${productId}"
            data-product-name="${productName}"
            data-product-price="${productPrice}"
            data-product-img="${productImage}">
            Añadir al Carrito - ${productPrice.toFixed(2)}€
          </button>
        </div>
      `;

      modal.style.display = 'flex';
    }

    if (e.target.matches('.close-modal')) {
      const modal = e.target.closest('#product-modal');
      if(modal) modal.style.display = 'none';
    }
  });
  
  // Cierra el modal si se hace clic fuera de su contenido.
  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });
  
  // Carga el carrito al iniciar la página.
  loadCart();

  // --- Funcionalidad de Registro ---
  const registerBtn = document.getElementById('register-btn');
  const registerModal = document.getElementById('register-modal');
  const closeRegisterModal = document.getElementById('close-register-modal');
  const registerForm = document.getElementById('register-form');

  // Muestra el modal de registro.
  registerBtn.addEventListener('click', () => {
    registerModal.style.display = 'block';
  });

  // Cierra el modal de registro con el botón de cerrar.
  closeRegisterModal.addEventListener('click', () => {
    registerModal.style.display = 'none';
  });

  // Cierra el modal de registro si se hace clic fuera de él.
  window.addEventListener('click', (e) => {
    if (e.target === registerModal) {
      registerModal.style.display = 'none';
    }
  });

  // Maneja el envío del formulario de registro.
  registerForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(registerForm);

    fetch('register.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      alert(data);
      if (data.includes('exitosamente')) {
        registerModal.style.display = 'none';
        registerForm.reset();
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Hubo un error al registrar el usuario.');
    });
  });
});
