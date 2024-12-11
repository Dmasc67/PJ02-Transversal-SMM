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

    // Actualizar el estado de la reserva a 'Cancelada'
    $update_query = "UPDATE tbl_reservas SET estado = 'Cancelada' WHERE id_reserva = :id_reserva";
    $stmt = $conexion->prepare($update_query);
    $stmt->bindParam(':id_reserva', $id_reserva);
    $stmt->execute();

    // Recoger los parámetros de filtro
    $usuario_filter = isset($_GET['usuario']) ? $_GET['usuario'] : '';
    $sala_filter = isset($_GET['sala']) ? $_GET['sala'] : '';
    $mesa_filter = isset($_GET['mesa']) ? $_GET['mesa'] : '';
    $fecha_reserva_filter = isset($_GET['fecha_reserva']) ? $_GET['fecha_reserva'] : '';
    $nombre_reserva_filter = isset($_GET['nombre_reserva']) ? $_GET['nombre_reserva'] : '';
    $estado_filter = isset($_GET['estado']) ? $_GET['estado'] : 'Pendiente';

    // Redirigir de vuelta a reservas.php con los filtros
    header("Location: reservas.php?mensaje=Reserva cancelada con éxito&usuario=$usuario_filter&sala=$sala_filter&mesa=$mesa_filter&fecha_reserva=$fecha_reserva_filter&nombre_reserva=$nombre_reserva_filter&estado=$estado_filter");
    exit();
} else {
    // Redirigir de vuelta a reservas.php si no se encuentra la reserva
    header("Location: reservas.php?error=Reserva no encontrada");
    exit();
}
?> 