<?php
session_start();
include './php/conexion.php';

// Obtener tipos de sala desde la base de datos
$query_tipos_sala = "SELECT DISTINCT tipo_sala FROM tbl_salas";
$stmt_tipos_sala = $conexion->query($query_tipos_sala);
$tipos_sala = $stmt_tipos_sala->fetchAll(PDO::FETCH_COLUMN);

// Crear sala
if (isset($_POST['crear_sala'])) {
    $nombre_sala = $_POST['nombre_sala'];
    $tipo_sala = $_POST['tipo_sala'];
    $capacidad = $_POST['capacidad'];

    // Manejo de la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen']['name'];
        $ruta_imagen = './img/salas/' . $imagen;
        
        // Crear el directorio si no existe
        if (!file_exists('./img/salas/')) {
            mkdir('./img/salas/', 0777, true);
        }
        
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_imagen);
    } else {
        $ruta_imagen = null;
    }

    $stmt = $conexion->prepare("INSERT INTO tbl_salas (nombre_sala, tipo_sala, capacidad, imagen) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre_sala, $tipo_sala, $capacidad, $ruta_imagen]);

    header("Location: crud_recursos.php");
    exit();
}

// Crear mesa
if (isset($_POST['crear_mesa'])) {
    $numero_mesa = $_POST['numero_mesa'];
    $id_sala = $_POST['id_sala'];
    $numero_sillas = $_POST['numero_sillas'];
    $estado = 'libre';

    $stmt = $conexion->prepare("INSERT INTO tbl_mesas (numero_mesa, id_sala, numero_sillas, estado) VALUES (?, ?, ?, ?)");
    $stmt->execute([$numero_mesa, $id_sala, $numero_sillas, $estado]);

    // Redirigir después de crear la mesa
    header("Location: crud_recursos.php");
    exit();
}

// Leer salas y mesas
$salas = $conexion->query("SELECT * FROM tbl_salas")->fetchAll(PDO::FETCH_ASSOC);
$mesas = $conexion->query("SELECT * FROM tbl_mesas")->fetchAll(PDO::FETCH_ASSOC);

// Obtener información de la sala para actualizar
$sala_a_actualizar = null;
if (isset($_POST['id_sala'])) {
    $id_sala = $_POST['id_sala'];
    $stmt_sala = $conexion->prepare("SELECT * FROM tbl_salas WHERE id_sala = ?");
    $stmt_sala->execute([$id_sala]);
    $sala_a_actualizar = $stmt_sala->fetch(PDO::FETCH_ASSOC);
}

// Actualizar sala
if (isset($_POST['actualizar_sala'])) {
    $id_sala = $_POST['id_sala'];
    $nombre_sala = $_POST['nombre_sala'];
    $tipo_sala = $_POST['tipo_sala'];
    $capacidad = $_POST['capacidad'];

    // Manejo de la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen']['name'];
        $ruta_imagen = 'ruta/donde/guardar/' . $imagen; // Cambia esta ruta según tu estructura de carpetas
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_imagen);
    } else {
        // Si no se subió una nueva imagen, mantener la imagen existente
        $stmt_imagen = $conexion->prepare("SELECT imagen FROM tbl_salas WHERE id_sala = ?");
        $stmt_imagen->execute([$id_sala]);
        $ruta_imagen = $stmt_imagen->fetchColumn();
    }

    $stmt = $conexion->prepare("UPDATE tbl_salas SET nombre_sala = ?, tipo_sala = ?, capacidad = ?, imagen = ? WHERE id_sala = ?");
    $stmt->execute([$nombre_sala, $tipo_sala, $capacidad, $ruta_imagen, $id_sala]);

    // Redirigir después de actualizar la sala
    header("Location: crud_recursos.php");
    exit();
}

// Actualizar mesa
if (isset($_POST['actualizar_mesa'])) {
    $id_mesa = $_POST['id_mesa'];
    $numero_mesa = $_POST['numero_mesa'];
    $id_sala = $_POST['id_sala'];
    $numero_sillas = $_POST['numero_sillas'];
    $estado = $_POST['estado'];

    $stmt = $conexion->prepare("UPDATE tbl_mesas SET numero_mesa = ?, id_sala = ?, numero_sillas = ?, estado = ? WHERE id_mesa = ?");
    $stmt->execute([$numero_mesa, $id_sala, $numero_sillas, $estado, $id_mesa]);

    // Redirigir después de actualizar la mesa
    header("Location: crud_recursos.php");
    exit();
}

// Eliminar sala
if (isset($_POST['eliminar_sala'])) {
    try {
        $id_sala = $_POST['id_sala'];
        
        // Primero eliminar las mesas asociadas
        $stmt_mesas = $conexion->prepare("DELETE FROM tbl_mesas WHERE id_sala = ?");
        $stmt_mesas->execute([$id_sala]);
        
        // Luego eliminar la sala
        $stmt = $conexion->prepare("DELETE FROM tbl_salas WHERE id_sala = ?");
        $stmt->execute([$id_sala]);

        $_SESSION['mensaje'] = "Sala eliminada con éxito";
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al eliminar la sala: " . $e->getMessage();
    }
    
    header("Location: crud_recursos.php");
    exit();
}

// Eliminar mesa
if (isset($_POST['eliminar_mesa'])) {
    try {
        $id_mesa = $_POST['id_mesa'];
        $stmt = $conexion->prepare("DELETE FROM tbl_mesas WHERE id_mesa = ?");
        $stmt->execute([$id_mesa]);

        $_SESSION['mensaje'] = "Mesa eliminada con éxito";
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al eliminar la mesa: " . $e->getMessage();
    }
    
    header("Location: crud_recursos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/swcrudrecu.js"></script>
    <title>CRUD de Recursos</title>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center text-white">Gestión de Recursos</h2>

        <!-- Formulario para crear sala -->
        <h3 class="text-white">Crear Sala</h3>
        <form method="POST" class="mb-4" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="text" name="nombre_sala" class="form-control" placeholder="Nombre de la sala" required>
            </div>
            <div class="mb-3">
                <select name="tipo_sala" class="form-select" required>
                    <option value="">Seleccionar tipo de sala</option>
                    <?php foreach ($tipos_sala as $tipo): ?>
                        <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <input type="number" name="capacidad" class="form-control" placeholder="Capacidad" required>
            </div>
            <div class="mb-3">
                <input type="file" name="imagen" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" name="crear_sala" class="btn btn-primary">Crear Sala</button>
        </form>

        <!-- Formulario para crear mesa -->
        <h3 class="text-white">Crear Mesa</h3>
        <form method="POST" class="mb-4" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="number" name="numero_mesa" class="form-control" placeholder="Número de mesa" required>
            </div>
            <div class="mb-3">
                <select name="id_sala" class="form-select" required>
                    <option value="">Seleccionar sala</option>
                    <?php foreach ($salas as $sala): ?>
                        <option value="<?php echo $sala['id_sala']; ?>"><?php echo $sala['nombre_sala']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <input type="number" name="numero_sillas" class="form-control" placeholder="Número de sillas" required>
            </div>
            <button type="submit" name="crear_mesa" class="btn btn-primary">Crear Mesa</button>
        </form>

        <h3 class="text-white">Lista de Salas</h3>
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Capacidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salas as $sala): ?>
                    <tr>
                        <td><?php echo $sala['id_sala']; ?></td>
                        <td><?php echo $sala['nombre_sala']; ?></td>
                        <td><?php echo $sala['tipo_sala']; ?></td>
                        <td><?php echo $sala['capacidad']; ?></td>
                        <td>
                            <form method="POST" class="form-eliminar-sala d-inline">
                                <input type="hidden" name="id_sala" value="<?php echo $sala['id_sala']; ?>">
                                <button type="submit" name="eliminar_sala" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                            <button class="btn btn-warning btn-sm" onclick="document.getElementById('update-sala-<?php echo $sala['id_sala']; ?>').style.display='block'">Actualizar</button>
                        </td>
                    </tr>
                    <div id="update-sala-<?php echo $sala['id_sala']; ?>" style="display:none;">
                        <form method="POST" class="mt-2">
                            <input type="hidden" name="id_sala" value="<?php echo $sala['id_sala']; ?>">
                            <h4 id="h4-cheto">Actualizar Sala: <?php echo htmlspecialchars($sala['nombre_sala']); ?></h4>
                            <input type="text" name="nombre_sala" class="form-control" value="<?php echo $sala['nombre_sala']; ?>" required>
                            <input type="text" name="tipo_sala" class="form-control" value="<?php echo $sala['tipo_sala']; ?>" required>
                            <input type="number" name="capacidad" class="form-control" value="<?php echo $sala['capacidad']; ?>" required>
                            <button type="submit" name="actualizar_sala" class="btn btn-success mt-2">Actualizar Sala</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h3 class="text-white">Lista de Mesas</h3>
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Número de Mesa</th>
                        <th>ID de Sala</th>
                        <th>Número de Sillas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mesas as $mesa): ?>
                    <tr>
                        <td><?php echo $mesa['id_mesa']; ?></td>
                        <td><?php echo $mesa['numero_mesa']; ?></td>
                        <td><?php echo $mesa['id_sala']; ?></td>
                        <td><?php echo $mesa['numero_sillas']; ?></td>
                        <td><?php echo $mesa['estado']; ?></td>
                        <td>
                            <form method="POST" class="form-eliminar-mesa d-inline">
                                <input type="hidden" name="id_mesa" value="<?php echo $mesa['id_mesa']; ?>">
                                <button type="submit" name="eliminar_mesa" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                            <button class="btn btn-warning btn-sm" onclick="document.getElementById('update-mesa-<?php echo $mesa['id_mesa']; ?>').style.display='block'">Actualizar</button>
                        </td>
                    </tr>
                    <div id="update-mesa-<?php echo $mesa['id_mesa']; ?>" style="display:none;">
                        <form method="POST" class="mt-2">
                            <input type="hidden" name="id_mesa" value="<?php echo $mesa['id_mesa']; ?>">
                            <h4>Actualizar Mesa: <?php echo htmlspecialchars($mesa['numero_mesa']); ?></h4>
                            <input type="number" name="numero_mesa" class="form-control" value="<?php echo $mesa['numero_mesa']; ?>" required>
                            <input type="number" name="numero_sillas" class="form-control" value="<?php echo $mesa['numero_sillas']; ?>" required>
                            <select name="estado" class="form-select" required>
                                <option value="libre" <?php if($mesa['estado'] == 'libre') echo 'selected'; ?>>Libre</option>
                                <option value="ocupada" <?php if($mesa['estado'] == 'ocupada') echo 'selected'; ?>>Ocupada</option>
                            </select>
                            <button type="submit" name="actualizar_mesa" class="btn btn-success mt-2">Actualizar Mesa</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php
    if (isset($_SESSION['mensaje'])) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '" . $_SESSION['mensaje'] . "'
            });
        </script>";
        unset($_SESSION['mensaje']);
    }
    ?>
</body>
</html>
