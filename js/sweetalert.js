document.addEventListener("DOMContentLoaded", () => {
    const usuario = document.body.getAttribute('data-usuario');
    const sweetalertMostrado = document.body.getAttribute('data-sweetalert') === 'true';
    const mesaSweetalertMostrado = document.body.getAttribute('data-mesa-sweetalert') === 'true';
    const errorMesaOcupada = document.body.getAttribute('data-error-mesa-ocupada') === 'true';

    if (usuario && !sweetalertMostrado) {
        Swal.fire({
            title: '¡Bienvenido!',
            text: `Hola ${usuario}, bienvenido/a al portal.`,
            icon: 'success',
            confirmButtonText: 'Gracias'
        }).then(() => {
            fetch('./php/marcar_sweetalert_mostrado.php');
        });
    }

    if (mesaSweetalertMostrado) {
        Swal.fire({
            title: 'Estado de la Mesa Cambiado',
            text: 'El estado de la mesa ha sido actualizado exitosamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            fetch('./php/limpiar_mesa_sweetalert.php');
        });
    }
}); 