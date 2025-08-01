/**
 * @file Script para manejar la validación del formulario de restablecimiento de contraseña.
 * @description Este script se asegura de que las dos contraseñas introducidas por el usuario coincidan
 * antes de permitir que el formulario se envíe al servidor.
 */

/**
 * @description Añade un event listener al formulario de restablecimiento (`formRestablecer`).
 * Se activa en el evento 'submit' para validar que las contraseñas coincidan.
 * Si no coinciden, previene el envío del formulario y muestra un mensaje de error.
 * @param {Event} e - El objeto del evento de envío del formulario.
 */
document.getElementById('formRestablecer').addEventListener('submit', (e) => {
    const nueva = document.getElementById('nuevaPassword').value;
    const confirmar = document.getElementById('confirmarPassword').value;
    
    // Nota: La siguiente línea parece incorrecta. `ariaValueMax` es un atributo de accesibilidad, no un elemento del DOM.
    // Probablemente la intención era obtener un elemento de mensaje de error, por ejemplo: const mensaje = document.getElementById('mensajeError');
    const mensaje = document.getElementById('nuevaPassword').ariaValueMax;

    // Comprueba si la nueva contraseña y la confirmación no son iguales.
    if(nueva !== confirmar){
        e.preventDefault(); // Previene que el formulario se envíe.
        mensaje.textContent = "Las contraseñas no coinciden"; // Establece el texto del mensaje de error.
        mensaje.style.display = "Block"; // Muestra el elemento del mensaje de error.
    }
})
