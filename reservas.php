<?php
session_start();
require_once('./php/conexion.php');

// Verificar sesión iniciada
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}

// Actualizar el estado de las reservas a Satisfactoria
$update_query = "UPDATE tbl_reservas
                 SET estado = 'Satisfactoria'
                 WHERE fecha_fin < NOW() AND estado = 'Pendiente'";
$conexion->exec($update_query);
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
    <script src="./js/auth.js"></script>
    <title>Historial de Reservas</title>
</head>

<body>
    <!-- Barra de navegación -->
    <div class="container">
        <nav class="navegacion">
            <div class="navbar-left">
                <a href="./menu.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./registro.php"><img src="./img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
                <a href="./reservas.php"><img src="./img/food.png" alt="Ícono adicional" class="navbar-icon"></a>
            </div>

            <div class="navbar-title">
                <h3>Historial de Reservas</h3>
            </div>

            <div class="navbar-right">
                <a href="./crud_usuarios.php"><img src="./img/users-alt.png" alt="Logout" class="navbar-icon"></a>
                <a href="./crud_recursos.php"><img src="./img/dinner-table.png" alt="Logout" class="navbar-icon"></a>
                <a href="./menu.php"><img src="./img/atras.png" alt="Logout" class="navbar-icon"></a>
                <a href="./salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>
    <br>
    <!-- Contenido principal -->
    <div id="reservas-container" class="container">
        <h2 class="text-white">Reservas Realizadas</h2>

        <!-- Formulario de filtros -->
        <form method="GET" action="reservas.php" class="mt-3">
            <div class="d-flex flex-wrap align-items-center">
                <!-- Filtro para nombre de reserva -->
                <div class="me-3">
                    <label for="nombre_reserva" class="text-white">Nombre Reserva:</label>
                    <input type="text" name="nombre_reserva" class="form-control form-control-sm" style="height: 40px; width: 200px;" value="<?php echo isset($_GET['nombre_reserva']) ? htmlspecialchars($_GET['nombre_reserva'], ENT_QUOTES) : ''; ?>" placeholder="Buscar...">
                </div>

                <div class="me-3">
                    <label for="usuario" class="text-white">Camarer@:</label>
                    <select name="usuario" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                        <option value="">Todos</option>
                        <?php
                        // Consulta para obtener usuarios
                        $query_usuarios = "SELECT id_usuario, nombre_user FROM tbl_usuarios";
                        $stmt_usuarios = $conexion->prepare($query_usuarios);
                        $stmt_usuarios->execute();
                        while ($usuario = $stmt_usuarios->fetch(PDO::FETCH_ASSOC)) {
                            $selected = isset($_GET['usuario']) && $_GET['usuario'] == $usuario['id_usuario'] ? 'selected' : '';
                            echo "<option value='{$usuario['id_usuario']}' $selected>{$usuario['nombre_user']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="me-3">
                    <label for="sala" class="text-white">Sala:</label>
                    <select name="sala" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                        <option value="">Todas</option>
                        <?php
                        // Consulta para obtener salas
                        $query_salas = "SELECT id_sala, nombre_sala FROM tbl_salas";
                        $stmt_salas = $conexion->prepare($query_salas);
                        $stmt_salas->execute();
                        while ($sala = $stmt_salas->fetch(PDO::FETCH_ASSOC)) {
                            $selected = isset($_GET['sala']) && $_GET['sala'] == $sala['id_sala'] ? 'selected' : '';
                            echo "<option value='{$sala['id_sala']}' $selected>{$sala['nombre_sala']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="me-3">
                    <label for="mesa" class="text-white">Mesa:</label>
                    <select name="mesa" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                        <option value="">Todas</option>
                        <?php
                        // Consulta para obtener mesas
                        $query_mesas = "SELECT id_mesa, numero_mesa FROM tbl_mesas";
                        $stmt_mesas = $conexion->prepare($query_mesas);
                        $stmt_mesas->execute();
                        while ($mesa = $stmt_mesas->fetch(PDO::FETCH_ASSOC)) {
                            $selected = isset($_GET['mesa']) && $_GET['mesa'] == $mesa['id_mesa'] ? 'selected' : '';
                            echo "<option value='{$mesa['id_mesa']}' $selected>{$mesa['numero_mesa']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Filtro para fecha -->
                <div class="me-3">
                    <label for="fecha_reserva" class="text-white">Fecha Reserva:</label>
                    <input type="date" name="fecha_reserva" class="form-control form-control-sm" style="height: 40px; width: 200px;" value="<?php echo isset($_GET['fecha_reserva']) ? $_GET['fecha_reserva'] : ''; ?>">
                </div>

                <!-- Filtro para estado de reserva -->
                <div class="me-3">
                    <label for="estado" class="text-white">Estado:</label>
                    <select name="estado" class="form-control form-control-sm" style="height: 40px; width: 200px;">
                        <option value="Pendiente" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="Satisfactoria" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Satisfactoria') ? 'selected' : ''; ?>>Satisfactoria</option>
                        <option value="Cancelada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="d-flex align-items-center mt-3">
                    <button type="submit" class="btn btn-primary btn-sm me-2" style="height: 40px; width: 200px; margin-top: 10px; margin-right: 10px; margin-bottom: 2px;">Filtrar</button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='reservas.php'" style="height: 40px; width: 200px; margin-top: 10px; margin-left: 7px;">Borrar Filtros</button>
                </div>
            </div>
        </form>

        <!-- Variables para los filtros -->
        <?php
        $usuario_filter = isset($_GET['usuario']) && !empty($_GET['usuario']) ? $_GET['usuario'] : '';
        $sala_filter = isset($_GET['sala']) && !empty($_GET['sala']) ? $_GET['sala'] : '';
        $mesa_filter = isset($_GET['mesa']) && !empty($_GET['mesa']) ? $_GET['mesa'] : '';
        $fecha_reserva_filter = isset($_GET['fecha_reserva']) && !empty($_GET['fecha_reserva']) ? $_GET['fecha_reserva'] : '';
        $nombre_reserva_filter = isset($_GET['nombre_reserva']) && !empty($_GET['nombre_reserva']) ? $_GET['nombre_reserva'] : '';
        $estado_filter = isset($_GET['estado']) && !empty($_GET['estado']) ? $_GET['estado'] : 'Pendiente'; // Predeterminado en 'Pendiente'
        ?>

        <!-- Consulta SQL para obtener el historial de reservas -->
        <?php
        $query_reservas = "SELECT r.id_reserva, u.nombre_user, s.nombre_sala, m.numero_mesa, r.nombre_reserva, r.fecha_reserva, r.fecha_inicio, r.fecha_fin, r.estado
                           FROM tbl_reservas r
                           JOIN tbl_mesas m ON r.id_mesa = m.id_mesa
                           JOIN tbl_salas s ON m.id_sala = s.id_sala
                           JOIN tbl_usuarios u ON r.id_usuario = u.id_usuario";

        $filters = [];
        if ($usuario_filter) {
            $filters[] = "u.id_usuario = :usuario";
        }
        if ($sala_filter) {
            $filters[] = "s.id_sala = :sala";
        }
        if ($mesa_filter) {
            $filters[] = "m.id_mesa = :mesa";
        }
        if ($fecha_reserva_filter) {
            $filters[] = "DATE(r.fecha_reserva) = :fecha_reserva"; // Filtrar por fecha
        }
        if ($nombre_reserva_filter) {
            $filters[] = "r.nombre_reserva LIKE :nombre_reserva"; // Filtrar por nombre de reserva
        }
        if ($estado_filter) {
            $filters[] = "r.estado = :estado"; // Filtrar por estado
        }

        if (!empty($filters)) {
            $query_reservas .= " WHERE " . implode(" AND ", $filters);
        }

        $stmt_reservas = $conexion->prepare($query_reservas);

        // Vincular parámetros
        if ($usuario_filter) {
            $stmt_reservas->bindParam(':usuario', $usuario_filter);
        }
        if ($sala_filter) {
            $stmt_reservas->bindParam(':sala', $sala_filter);
        }
        if ($mesa_filter) {
            $stmt_reservas->bindParam(':mesa', $mesa_filter);
        }
        if ($fecha_reserva_filter) {
            $stmt_reservas->bindParam(':fecha_reserva', $fecha_reserva_filter);
        }
        if ($nombre_reserva_filter) {
            $nombre_reserva_filter = "%$nombre_reserva_filter%"; // Agregar comodines para búsqueda parcial
            $stmt_reservas->bindParam(':nombre_reserva', $nombre_reserva_filter);
        }
        if ($estado_filter) {
            $stmt_reservas->bindParam(':estado', $estado_filter);
        }

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
                        <th>Estado</th>
                        <th>Acciones</th>
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
                        <td>{$reserva['estado']}</td>
                        <td>
                            <form method='POST' action='cambiar_estado.php' style='display:inline;' class='delete-form'>
                                <input type='hidden' name='id_reserva' value='{$reserva['id_reserva']}'>
                                <button type='button' class='btn btn-warning btn-sm delete-button' 
                                    " . ($reserva['estado'] === 'Cancelada' ? 'disabled' : '') . ">
                                    Cancelar
                                </button>
                            </form>
                        </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="./js/swborrarreserva.js"></script>
</body>

</html>