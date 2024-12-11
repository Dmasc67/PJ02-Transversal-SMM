<?php
session_start();
require_once('./php/conexion.php');

// Verificar sesión iniciada
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/menu.css"> <!-- Archivo de estilos para el menú y la página -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Historial de Reservas</title>
</head>

<body>
    <!-- Barra de navegación -->
    <div class="container">
        <nav class="navegacion">
            <div class="navbar-left">
                <a href="./menu.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
            </div>

            <div class="navbar-title">
                <h3>Historial de Reservas</h3>
            </div>

            <div class="navbar-right" style="margin-right: 18px;">
                <a href="./menu.php"><img src="./img/atras.png" alt="Logout" class="navbar-icon"></a>
            </div>

            <div class="navbar-right">
                <a href="./salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>
    <br>
    <!-- Contenido principal -->
    <div id="reservas-container" class="container">
        <h2 class="text-white">Reservas Realizadas</h2>

        <!-- Consulta SQL para obtener el historial de reservas -->
        <?php
        $query_reservas = "SELECT u.nombre_user, s.nombre_sala, m.numero_mesa, r.nombre_reserva, r.fecha_reserva, r.fecha_inicio, r.fecha_fin
                           FROM tbl_reservas r
                           JOIN tbl_mesas m ON r.id_mesa = m.id_mesa
                           JOIN tbl_salas s ON m.id_sala = s.id_sala
                           JOIN tbl_usuarios u ON r.id_usuario = u.id_usuario";

        $stmt_reservas = $conexion->prepare($query_reservas);
        $stmt_reservas->execute();
        ?>

        <!-- Mostrar resultados en tabla -->
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Nombre Reserva</th>
                        <th>Camarero</th>
                        <th>Sala</th>
                        <th>Número de Mesa</th>
                        <th>Fecha Reserva</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($reserva = $stmt_reservas->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                        <td>{$reserva['nombre_reserva']}</td>
                        <td>{$reserva['nombre_user']}</td>
                        <td>{$reserva['nombre_sala']}</td>
                        <td>{$reserva['numero_mesa']}</td>
                        <td>{$reserva['fecha_reserva']}</td>
                        <td>{$reserva['fecha_inicio']}</td>
                        <td>{$reserva['fecha_fin']}</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="./js/sweetalert.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</body>

</html>