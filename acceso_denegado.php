<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Acceso Denegado</title>
</head>
<body>
    <div class="container mt-5 text-center">
        <h2 style="color: white;">Acceso Denegado</h2>
        <p style="color: white;">No se puede acceder a esta página debido a falta de permisos.</p>
        <a href="./menu.php" class="btn btn-primary">Volver al Menú</a>
    </div>
</body>
</html>
