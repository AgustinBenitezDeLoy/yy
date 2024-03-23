<?php
// Conexión a la base de datos
$host = "localhost";
$username = "root"; // Asegúrate de usar el usuario correcto de tu DB
$password = ""; // Asegúrate de usar la contraseña correcta de tu DB
$dbname = "reentraste"; // Asegúrate de que el nombre de la DB sea correcto

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recoger datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo = $_POST['correo'];
$contraseña = $_POST['contraseña'];
$cedula = $_POST['cedula'];

// Verificar si el correo ya existe
$sql = "SELECT id FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "El usuario ya existe";
} else {
    // Encriptar contraseña
    $contraseñaEncriptada = password_hash($contraseña, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario con rol 'usuario'
    $sql = "INSERT INTO usuarios (nombre, apellido, correo, contraseña, cedula, rol) VALUES (?, ?, ?, ?, ?, 'usuario')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $apellido, $correo, $contraseñaEncriptada, $cedula);

    if ($stmt->execute()) {
        echo "Nuevo registro creado con éxito";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
