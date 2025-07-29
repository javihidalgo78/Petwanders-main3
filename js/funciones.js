	import { productos } from './productos.js';


  const modal = document.getElementById('product-modal');
 

  //evento que se ejecuta cuando el html se ha cargado
  document.addEventListener('DOMContentLoaded', function() {
  /**
   * **********SLIDER***************
   */
		let currentSlide = 0;
		const slides = document.querySelectorAll('.slider .slide');
	  
		function showNextSlide() {
		  slides[currentSlide].classList.remove('active');
		  currentSlide = (currentSlide + 1) % slides.length;
		  slides[currentSlide].classList.add('active');
		}
	  
		setInterval(showNextSlide, 6000); // Cambia cada 8 segundos

    // Funcionalidad del slider (código existente)
    document.addEventListener('DOMContentLoaded', function() {
      const slides = document.querySelectorAll('.slider .slide');
      let currentSlide = 0;
      
      function showSlide(n) {
        slides.forEach(slide => slide.classList.remove('active'));
        slides[n].classList.add('active');
      }
      
      function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
      }
      
      setInterval(nextSlide, 3000);
    });
/** 
   * **********FIN SLIDER***************
  */

/**
   * **********CARRITO COMPRAS***************
 */
    // Funcionalidad del carrito de compras

      // Elementos del DOM
      const cartIcon = document.getElementById('cart-icon');
      const cartContainer = document.getElementById('cart-container');
      const closeCart = document.getElementById('close-cart');
      const backdrop = document.getElementById('backdrop');
      const cartItems = document.getElementById('cart-items');
      const cartCount = document.getElementById('cart-count');
      const cartTotal = document.getElementById('cart-total');
      const checkoutBtn = document.getElementById('checkout-btn');
      const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
      const showProduct = document.querySelectorAll('.show-product');
      
      // Carrito de compras
      let cart = [];
      
      // Cargar carrito desde localStorage
      function loadCart() {
        const savedCart = localStorage.getItem('petwandersCart');
        if (savedCart) {
          cart = JSON.parse(savedCart);
          updateCartUI();
        }
      }
      
      // Guardar carrito en localStorage
      function saveCart() {
        localStorage.setItem('petwandersCart', JSON.stringify(cart));
      }
      
      // Actualizar la interfaz del carrito
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
        
        // Añadir controladores de eventos para los botones
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
      
      // Añadir producto al carrito
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
      
      // Aumentar cantidad
      function increaseQuantity(index) {
        cart[index].quantity += 1;
        updateCartUI();
        saveCart();
      }
      
      // Disminuir cantidad
      function decreaseQuantity(index) {
        if (cart[index].quantity > 1) {
          cart[index].quantity -= 1;
        } else {
          removeItem(index);
        }
        updateCartUI();
        saveCart();
      }
      
      // Eliminar producto
      function removeItem(index) {
        cart.splice(index, 1);
        updateCartUI();
        saveCart();
      }
      
      // Mostrar carrito
      function showCart() {
        cartContainer.classList.add('active');
        backdrop.classList.add('active');
      }
      
      // Ocultar carrito
      function hideCart() {
        cartContainer.classList.remove('active');
        backdrop.classList.remove('active');
      }
      
      // Event Listeners
      cartIcon.addEventListener('click', showCart);
      closeCart.addEventListener('click', hideCart);
      backdrop.addEventListener('click', hideCart);
      
      // Event Listeners para botones "Añadir al Carrito"
      addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
          const productId = this.dataset.productId;
          const productName = this.dataset.productName;
          const productPrice = parseFloat(this.dataset.productPrice);
          const productImage = this.dataset.productImg;
          
          // Obtener talla seleccionada
          const sizeSelect = document.querySelector(`.size-select[data-product-id="${productId}"]`);
          const productSize = sizeSelect.value;
          
          if (!productSize) {
            alert('Por favor, selecciona una talla/capacidad');
            return;
          }
          
          addToCart(productId, productName, productPrice, productImage, productSize);
        });
      });

      
      // Función para finalizar compra
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
      
      // Cargar carrito al iniciar
      loadCart();
    });


/**
 * *************FIN CARRITO****************
 */


/**
 * *************MOSTRAR PRODUCTOS EN VENTANA****************
 */
const contenedor = document.getElementById('productos-container');

productos.forEach(producto => {
  contenedor.innerHTML += displayProduct(producto, false)
});

/**
 * 
 * @param {recibe un producto} producto 
 * @param {boolean} modal 
 * @returns html para mostrar el producto en pantalla
 * @description Distingue entre modal o escritorio para maquetar el producto
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
  
  // Si estamos en la modal, mostramos las características, pero no la descripción
  // Si no estamos en la modal, mostramos la descripción, pero no las características
  const descripcion = modal ? '' : `${producto.descripcion}`;
  const features = modal ? convertirFeaturesALista(producto.features) : "";
  
  // Usar un ID único para el selector y el botón en la modal
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

// Modificar el evento para cuando se hace clic en la imagen del producto
document.querySelectorAll('.show-product').forEach(enlace => {
  enlace.addEventListener('click', function () {
    const productId = this.dataset.productId;
    const producto = productos.find(p => p.id === productId);
    
    if (!producto) return;
    const boton = `<span class="close-modal">&times;</span>`;
    const contenido = displayProduct(producto, true);

    document.getElementById("modal-content").innerHTML = boton + contenido;
    modal.style.display = 'flex';
    
    // Agregar el evento al botón DESPUÉS de que se haya insertado en el DOM
    const modalAddToCartBtn = document.querySelector('.modal-add-to-cart-btn');
    if (modalAddToCartBtn) {
      modalAddToCartBtn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const productName = this.dataset.productName;
        const productPrice = parseFloat(this.dataset.productPrice);
        const productImage = this.dataset.productImg;
        
        // Obtener talla/capacidad/color seleccionada usando el ID único del selector
        const sizeSelect = document.querySelector(`#modal-size-select-${productId}`);
        const productSize = sizeSelect ? sizeSelect.value : '';
        
        if (!productSize) {
          alert('Por favor, selecciona una opción');
          return;
        }
        
        // Asegurarnos de que estamos llamando a la función correcta
        // Y que está definida en el ámbito adecuado
        if (typeof addToCart === 'function') {
          addToCart(productId, productName, productPrice, productImage, productSize);
          modal.style.display = 'none'; // Cerrar modal tras añadir
        } else {
          console.error('La función addToCart no está disponible');
        }
      });
    }

    document.querySelector('.close-modal').addEventListener('click', () => {
      modal.style.display = 'none';
    });
  });
});

// function displayProduct(producto, modal){
  
//   let opciones = [];
//   let tipoSelect = "";
  
//   if (producto.tallas) {
//     opciones = producto.tallas;
//     tipoSelect = "talla";
//   } else if (producto.capacidades) {
//     opciones = producto.capacidades;
//     tipoSelect = "capacidad";
//   } else if (producto.colores) {
//     opciones = producto.colores;
//     tipoSelect = "color";
//   }
  
//   const opcionesHTML = opciones.map(opcion =>
//     `<option value="${opcion}">${opcion}</option>`
//   ).join("");

//   const amazonUrl = producto.amazonUrl || "https://www.amazon.com/s?k=" + encodeURIComponent(producto.nombre);
//   const descripcion = modal?'':`${producto.descripcion}`;
//   const features = modal? convertirFeaturesALista(producto.features) : "";
//   const cardHTML = `
//     <div class="card">
//     <a 
//       data-product-id="${producto.id}"
//       data-product-name="${producto.nombre}"
//       data-product-price="${producto.precio}"
//       data-product-img="${producto.imagen}"
//       data-product-description="${producto.descripcion}"
//      class="show-product">
//     <img src="${producto.imagen}" alt="${producto.nombre}">
//     </a>
      
//       <div>
//         <h3>${producto.nombre}</h3>
//         <p>${descripcion}</p>
//         ${features}
//         <div class="buy-options">
//           <select class="size-select" data-product-id="${producto.id}">
//             <option value="">Seleccionar ${tipoSelect}</option>
//             ${opcionesHTML}
//           </select>
//           <button
//             class="add-to-cart-btn"
//             data-product-id="${producto.id}"
//             data-product-name="${producto.nombre}"
//             data-product-price="${producto.precio}"
//             data-product-img="${producto.imagen}">
//             Añadir al Carrito - ${producto.precio.toFixed(2)}€
//           </button>
//         </div>
//         <div class="amazon-link">
//           <a href="${amazonUrl}" target="_blank" rel="noopener noreferrer">
//             <img src="images/amazon-icon.png" alt="Comprar en Amazon" title="Comprar en Amazon" class="amazon-icon">
//           </a>
//         </div>
//       </div>
//     </div>
//   `;


//   if(modal){
//     // Añadir evento al botón de la modal
// const addToCartBtnModal = document.querySelector('.add-to-cart-btn');
// if (addToCartBtnModal) {
//   addToCartBtnModal.addEventListener('click', function(e) {
// console.log(e)
//     const productId = this.dataset.productId;
//     const productName = this.dataset.productName;
//     const productPrice = parseFloat(this.dataset.productPrice);
//     const productImage = this.dataset.productImg;
//     // Obtener talla/capacidad/color seleccionada
//     const sizeSelect = document.querySelector('.size-select[data-product-id="' + productId + '"]');
//     const productSize = sizeSelect ? sizeSelect.value : '';
//     if (!productSize) {
//       alert('Por favor, selecciona una opción');
//       return;
//     }
//     addToCart(productId, productName, productPrice, productImage, productSize);
//     modal.style.display = 'none'; // Opcional: cerrar modal tras añadir
//   });
// }
//   }
//   return cardHTML;
// }

/**
 * *************FIN MOSTRAR PRODUCTOS EN VENTANA****************
 */


// Elementos del producto en la modal

  // Abrir modal al hacer clic en imagen
  document.querySelectorAll('.show-product').forEach(enlace => {
    enlace.addEventListener('click', function () {
      const productId = this.dataset.productId;
      const producto = productos.find(p => p.id === productId);
      
      if (!producto) return;
      const boton = `<span class="close-modal">&times;</span>`;
      const contenido = displayProduct(producto, true);

      document.getElementById("modal-content").innerHTML =  boton + contenido;
      modal.style.display = 'flex';
      //TODO: crear evento para añadir al carrito

      document.querySelector('.close-modal').addEventListener('click', () => {
    modal.style.display = 'none';
  });
    });
  });
  
  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });


// También puedes cerrar el modal haciendo clic fuera del contenido
window.addEventListener("click", function(event) {
  const modal = document.getElementById("product-modal");
  if (event.target === modal) {
    modal.style.display = "none";
  }
});



// Función para convertir el texto de características en una lista HTML
function convertirFeaturesALista(featuresTexto) {
  return `<ul class = "features">${
    featuresTexto
      .split(/\r?\n/)
      .filter(linea => linea.trim().startsWith("•"))
      .map(linea => `<li>${linea.replace(/^•\s*/, "")}</li>`)
      .join("")
  }</ul>`;
}



const modalAddToCartBtn = document.getElementById('modal-add-to-cart');

const carrito = [];

function agregarAlCarrito(item) {
  const existente = carrito.find(p => p.id === item.id && p.opcion === item.opcion);
  if (existente) {
    existente.cantidad += 1;
  } else {
    carrito.push(item);
  }
  console.log("Carrito actualizado:", carrito);
}

