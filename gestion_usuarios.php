<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    exit('Acceso restringido solo a administradores.');
}

// Datos para la conexión a la base de datos
$host = 'localhost';
$database = 'reentraste'; // Asegúrate de que esta sea el nombre correcto de tu base de datos
$username = 'root'; // Usuario predeterminado de MySQL en XAMPP
$password = ''; // Contraseña predeterminada de MySQL en XAMPP

$conn = new mysqli($host, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar que los campos no estén vacíos
if (!empty($_POST['nombre']) && !empty($_POST['correo']) && !empty($_POST['rol'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];

    // Preparar consulta SQL para insertar el nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $correo, $rol);

    if ($stmt->execute()) {
        echo "Nuevo usuario agregado con éxito.";
    } else {
        echo "Error al agregar el usuario: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Por favor, completa todos los campos del formulario.";
}

$conn->close();
?>
