document.addEventListener('DOMContentLoaded', function() {
    // Función para manejar la eliminación
    function handleDelete(formElement, mensaje) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: mensaje,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear y enviar formulario manualmente
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';

                // Copiar los inputs hidden del formulario original
                const inputs = formElement.querySelectorAll('input[type="hidden"]');
                inputs.forEach(input => {
                    const clonedInput = input.cloneNode(true);
                    form.appendChild(clonedInput);
                });

                // Agregar el botón submit correspondiente
                const submitInput = document.createElement('input');
                submitInput.type = 'hidden';
                submitInput.name = formElement.querySelector('button[type="submit"]').name;
                submitInput.value = '1';
                form.appendChild(submitInput);

                // Agregar el formulario al documento y enviarlo
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Manejar clicks en botones de eliminar sala
    document.querySelectorAll('.form-eliminar-sala button[type="submit"]').forEach(button => {
        button.onclick = function(e) {
            e.preventDefault();
            handleDelete(this.closest('form'), "¡No podrás revertir esta acción!");
        };
    });

    // Manejar clicks en botones de eliminar mesa
    document.querySelectorAll('.form-eliminar-mesa button[type="submit"]').forEach(button => {
        button.onclick = function(e) {
            e.preventDefault();
            handleDelete(this.closest('form'), "¡No podrás revertir esta eliminación!");
        };
    });
});
