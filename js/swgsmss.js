// Función para mostrar el SweetAlert cuando la mesa está ocupada
function mostrarSweetAlertMesaOcupada() {
    Swal.fire({
        icon: 'error',
        title: '¡Mesa Ocupada!',
        text: 'La mesa ya está ocupada en ese horario. Por favor, elige otro horario.',
        confirmButtonText: 'Entendido'
    }).then(() => {
        window.location.href = 'reservas.php'; // Redirigir a reservas.php después de cerrar el SweetAlert
    });
}
