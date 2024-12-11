<?php
session_start();
require_once('./php/conexion.php');

// Verificar sesión iniciada
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}

// Verificar si se ha enviado el ID de la reserva
if (isset($_POST['id_reserva'])) {
    $id_reserva = $_POST['id_reserva'];

    // Consulta para borrar la reserva
    $query = "DELETE FROM tbl_reservas WHERE id_reserva = :id_reserva";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':id_reserva', $id_reserva);

    if ($stmt->execute()) {
        // Redirigir de vuelta a reservas.php con un mensaje de éxito
        header("Location: reservas.php?mensaje=Reserva borrada con éxito");
    } else {
        // Redirigir de vuelta a reservas.php con un mensaje de error
        header("Location: reservas.php?error=Error al borrar la reserva");
    }
} else {
    // Redirigir de vuelta a reservas.php si no se proporciona el ID
    header("Location: reservas.php?error=ID de reserva no proporcionado");
}
?> 