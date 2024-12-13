<?php
include './php/conexion.php';

// Verificar el tipo de usuario
session_start(); // Asegúrate de que la sesión esté iniciada
if ($_SESSION['tipo_usuario'] === 'camarero') {
    header("Location: acceso_denegado.php"); // Redirigir a una página de acceso denegado
    exit();
}

// Crear usuario
if (isset($_POST['crear'])) {
    $nombre_user = $_POST['nombre_user'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $tipo_usuario = $_POST['tipo_usuario'];

    try {
        $stmt = $conexion->prepare("INSERT INTO tbl_usuarios (nombre_user, contrasena, tipo_usuario) VALUES (?, ?, ?)");
        $stmt->execute([$nombre_user, $contrasena, $tipo_usuario]);
        // Redirigir después de crear el usuario
        header("Location: crud_usuarios.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al crear el usuario: " . $e->getMessage();
    }
}

// Leer usuarios
$usuarios = $conexion->query("SELECT * FROM tbl_usuarios")->fetchAll(PDO::FETCH_ASSOC);

// Actualizar usuario
if (isset($_POST['actualizar'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre_user = $_POST['nombre_user'];
    $tipo_usuario = $_POST['tipo_usuario'];

    try {
        $stmt = $conexion->prepare("UPDATE tbl_usuarios SET nombre_user = ?, tipo_usuario = ? WHERE id_usuario = ?");
        $stmt->execute([$nombre_user, $tipo_usuario, $id_usuario]);
        // Redirigir después de actualizar el usuario
        header("Location: crud_usuarios.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al actualizar el usuario: " . $e->getMessage();
    }
}

// Eliminar usuario
if (isset($_POST['eliminar'])) {
    $id_usuario = $_POST['id_usuario'];
    $stmt = $conexion->prepare("DELETE FROM tbl_usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    // Redirigir después de eliminar el usuario
    header("Location: crud_usuarios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD de Usuarios</title>
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="./js/auth.js"></script> <!-- Asegúrate de incluir tu archivo JS -->
</head>
<body>
<div class="container">
        <nav class="navegacion">
            <!-- Sección izquierda con el logo grande y el ícono adicional más pequeño -->
            <div class="navbar-left">
                <a href="./menu.php"><img src="./img/logo.png" alt="Logo de la Marca" class="logo" style="width: 100%;"></a>
                <a href="./registro.php"><img src="./img/lbook.png" alt="Ícono adicional" class="navbar-icon"></a>
                <a href="./reservas.php"><img src="./img/food.png"  alt="Ícono adicional" class="navbar-icon"></a>
            </div>

            <!-- Título en el centro -->
            <div class="navbar-title">
                <h3>Usuarios</h3>
            </div>

            <!-- Icono de logout a la derecha -->
            <div class="navbar-right">
                <a href="./crud_usuarios.php"><img src="./img/users-alt.png" alt="Logout" class="navbar-icon"></a>
                <a href="./crud_recursos.php"><img src="./img/dinner-table.png" alt="Logout" class="navbar-icon"></a>
                <a href="./menu.php"><img src="./img/atras.png" alt="Logout" class="navbar-icon"></a>
                <a href="./salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>
    <div class="container mt-5">
        <h2 class="text-center" style="color: white;">Gestión de Usuarios</h2>

        <!-- Formulario para crear usuario -->
        <form method="POST" class="mb-4" onsubmit="return validateForm();">
            <div class="mb-3">
                <input type="text" name="nombre_user" class="form-control" placeholder="Nombre de usuario" required oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');">
            </div>
            <div class="mb-3">
                <input type="password" name="contrasena" class="form-control" placeholder="Contraseña" required oninput="validatePassword(this);">
                <div id="passwordError" style="color: red;"></div>
            </div>
            <div class="mb-3">
                <select name="tipo_usuario" class="form-select" required>
                    <option value="camarero">Camarero</option>
                    <option value="gerente">Gerente</option>
                    <option value="mantenimiento">Mantenimiento</option>
                </select>
            </div>
            <button type="submit" name="crear" class="btn btn-primary">Crear Usuario</button>
        </form>

        <h3 class="text-center" style="color: white;">Lista de Usuarios</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo de Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario['id_usuario']; ?></td>
                    <td><?php echo $usuario['nombre_user']; ?></td>
                    <td><?php echo $usuario['tipo_usuario']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                            <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                        <button class="btn btn-warning btn-sm" onclick="document.getElementById('update-<?php echo $usuario['id_usuario']; ?>').style.display='block'">Actualizar</button>
                    </td>
                </tr>
                <div id="update-<?php echo $usuario['id_usuario']; ?>" style="display:none;">
                    <form method="POST" class="mt-2" onsubmit="return validateUpdateForm(this);">
                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                        <h4 style="color: white;">Actualizar Usuario: <?php echo htmlspecialchars($usuario['nombre_user']); ?></h4>
                        
                        <!-- Texto e inputs para actualizar -->
                        <p style="color: white;">Nombre de Usuario:</p>
                        <input type="text" name="nombre_user" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre_user']); ?>" required oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');">
                        <br>
                        <p style="color: white;">Tipo de Usuario:</p>
                        <select name="tipo_usuario" class="form-select" required>
                            <option value="camarero" <?php if($usuario['tipo_usuario'] == 'camarero') echo 'selected'; ?>>Camarero</option>
                            <option value="gerente" <?php if($usuario['tipo_usuario'] == 'gerente') echo 'selected'; ?>>Gerente</option>
                            <option value="mantenimiento" <?php if($usuario['tipo_usuario'] == 'mantenimiento') echo 'selected'; ?>>Mantenimiento</option>
                        </select>
                        <button type="submit" name="actualizar" class="btn btn-success mt-2">Actualizar Usuario</button>
                        <br>
                    </form>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <div id="passwordError" style="color: red;"></div>
    <script>
    function validateUpdateForm(form) {
        const nombreUserInput = form.nombre_user.value;
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/; // Permitir solo letras y espacios

        if (!regex.test(nombreUserInput)) {
            alert("El nombre de usuario solo puede contener letras y espacios.");
            return false; // Evitar el envío del formulario
        }
        return true; // Permitir el envío del formulario
    }
    </script>
</body>
</html>
