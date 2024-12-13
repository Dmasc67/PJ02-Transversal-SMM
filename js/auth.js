document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');

    if (error) {
        if (error === 'contrasena_incorrecta' || error === 'usuario_no_encontrado') {
            document.getElementById('usuarioError').textContent = 'Usuario o contraseña incorrecto';
            document.getElementById('contraError').textContent = 'Usuario o contraseña incorrecto';
        } else if (error === 'campos_vacios') {
            document.getElementById('usuarioError').textContent = 'Por favor, complete todos los campos';
            document.getElementById('contraError').textContent = 'Por favor, complete todos los campos';
        }
    }

    // Validación para el campo de nombre reserva
    const nombreReservaInput = document.querySelector('input[name="nombre_reserva"]');
    if (nombreReservaInput) {
        nombreReservaInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, ''); // Permitir solo letras y espacios
        });
    }

    const numeroMesaInput = document.querySelector('input[name="numero_mesa"]');
    const numeroSillasInput = document.querySelector('input[name="numero_sillas"]');

    if (numeroMesaInput) {
        numeroMesaInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); // Permitir solo números
        });
    }

    if (numeroSillasInput) {
        numeroSillasInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); // Permitir solo números
        });
    }
});

// Función para validar la contraseña
function validatePassword(input) {
    const password = input.value;
    const errorMessage = document.getElementById('passwordError'); // Asegúrate de tener un elemento para mostrar el error

    // Expresión regular para validar la contraseña
    const isValid = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/.test(password);

    if (!isValid) {
        errorMessage.textContent = 'La contraseña debe tener al menos 8 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y un número.';
    } else {
        errorMessage.textContent = ''; // Limpiar el mensaje de error si es válido
    }
}

