<?php
session_start();
require_once('./php/conexion.php');

// Verificar si la variable de sesión 'Usuario' está configurada
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=sesion_no_iniciada");
    exit();
}

// Verificar si el SweetAlert ya se mostró
if (!isset($_SESSION['sweetalert_mostrado'])) {
    $_SESSION['sweetalert_mostrado'] = false;
}

// Mostrar pop-up si hay un error de hora de reserva
if (isset($_SESSION['error_hora_reserva'])) {
    echo "<script>
            window.onload = function() {
                setTimeout(function() {
                    alert('La hora de reserva debe estar entre las 9 AM y las 10 PM.');
                }, 100); // Retraso de 100 ms antes de mostrar el pop-up
            };
          </script>";
    unset($_SESSION['error_hora_reserva']); // Limpiar la variable de sesión
}
try {
    $usuario = $_SESSION['usuario'];
    $sql = "SELECT id_usuario FROM tbl_usuarios WHERE nombre_user = :usuario";
    $stmt_usuario = $conexion->prepare($sql);
    $stmt_usuario->bindParam(':usuario', $usuario);
    $stmt_usuario->execute();
    $stmt_usuario->bindColumn(1, $id_usuario);
    $stmt_usuario->fetch(PDO::FETCH_BOUND);
    $_SESSION['id_usuario'] = $id_usuario;
    $stmt_usuario = null;
} catch (PDOException $e) {
    die("Error en la base de datos: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body data-usuario="<?php echo htmlspecialchars($_SESSION['Usuario'], ENT_QUOTES, 'UTF-8'); ?>" data-sweetalert="<?php echo $_SESSION['sweetalert_mostrado'] ? 'true' : 'false'; ?>">
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
                <h3>Bienvenido <?php if (isset($_SESSION['usuario'])) {
                                    echo $_SESSION['usuario'];
                                } ?></h3>
            </div>

            <!-- Icono de logout a la derecha -->
            <div class="navbar-right">
                <a href="./crud_usuarios.php"><img src="./img/users-alt.png" alt="Logout" class="navbar-icon"></a>
                <a href="./crud_recursos.php"><img src="./img/dinner-table.png" alt="Logout" class="navbar-icon"></a>
                <a href="./salir.php"><img src="./img/logout.png" alt="Logout" class="navbar-icon"></a>
            </div>
        </nav>
    </div>
    <!------------FIN BARRA DE NAVEGACION--------------------->
    <div class="container-menu">
        <section>
            <a class="image-container" href="./seleccionar_sala?categoria=Comedor">
                <img src="./img/Comedor 1.jpg" alt="" id="comedor">
                <div class="text-overlay">Comedor</div>
            </a>
            <a class="image-container" href="./seleccionar_sala?categoria=Privada">
                <img src="./img/Sala Privada 1.jpg" alt="" id="privada">
                <div class="text-overlay">Sala privada</div>
            </a>
            <a class="image-container" href="./seleccionar_sala?categoria=Terraza">
                <img src="./img/Terraza 1.jpg" alt="" id="terraza">
                <div class="text-overlay">Terraza</div>
            </a>
        </section>
    </div>

    <script src="./js/sweetalert.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</body>

</html>