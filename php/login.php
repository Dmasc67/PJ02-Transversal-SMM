<?php
require_once('./conexion.php'); // Asegúrate de que este archivo esté configurado correctamente
session_start();
if (isset($_POST['btn_iniciar_sesion']) && !empty($_POST['Usuario']) && !empty($_POST['Contra'])) {
    $contra = isset($_POST['Contra']) ? htmlspecialchars($_POST['Contra']) : '';
    $usuario = isset($_POST['Usuario']) ? htmlspecialchars($_POST['Usuario']) : '';
    $_SESSION['usuario'] = $usuario;
    try {
        $conexion->beginTransaction(); // Asegúrate de que $conexion esté definido

        $sql = "SELECT nombre_user, contrasena FROM tbl_usuarios WHERE nombre_user = :usuario";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            if (password_verify($contra, $resultado['contrasena'])) {
                $_SESSION['Usuario'] = $usuario;
                $conexion->commit();
                header("Location: ../menu.php");    
                exit();
            } else {
                $conexion->rollBack();
                header('Location:../index.php?error=contrasena_incorrecta');
            }
        } else {
            $conexion->rollBack();
            header('Location:../index.php?error=usuario_no_encontrado');
        }

        $stmt = null;
    } catch (Exception $e) {
        $conexion->rollBack();
        echo "Se produjo un error: " . htmlspecialchars($e->getMessage());
    }
} else {
    header('Location: ../index.php?error=campos_vacios');
}