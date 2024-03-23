<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$dbname = "reentraste";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = ""; // Mensaje para el usuario
$urlRedireccion = ""; // URL de redirección

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    $stmt = $conn->prepare("SELECT id, rol, contraseña, validado FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($usuario = $resultado->fetch_assoc()) {
        if (password_verify($contraseña, $usuario['contraseña'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['rol'] = $usuario['rol'];

            switch ($usuario['validado']) {
                case 'validado':
                    $mensaje = "Sesión iniciada con éxito. Redirigiendo...";
                    $urlRedireccion = ($_SESSION['rol'] === 'administrador') ? 'panel_admin.php' : 'pagina_vendedor.php';
                    break;
                case 'rechazado':
                    $mensaje = "Tu cuenta ha sido rechazada.";
                    break;
                default:
                    $mensaje = "Tu cuenta está pendiente de verificación.";
                    break;
            }
        } else {
            $mensaje = "Credenciales inválidas.";
        }
    } else {
        $mensaje = "Credenciales inválidas.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
</head>
<body>
    <p><?php echo $mensaje; ?></p>
    <?php if (!empty($urlRedireccion)) : ?>
        <script>
            setTimeout(function() {
                window.location.href = '<?php echo $urlRedireccion; ?>';
            }, 2000); // Redirige después de 2 segundos
        </script>
    <?php endif; ?>
</body>
</html>
