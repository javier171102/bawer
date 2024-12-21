<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Usuario por defecto de phpMyAdmin
$password = ""; // Contraseña por defecto de phpMyAdmin
$dbname = "notaria_db"; // Nombre de la base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recibir los datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$email = $_POST['email'];
$dni = $_POST['dni'];
$telefono = $_POST['telefono'];
$contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT); // Cifrar la contraseña

// Preparar y ejecutar la consulta
$sql = "INSERT INTO usuarios (nombre, apellido, email, dni, telefono, contraseña) 
        VALUES ('$nombre', '$apellido', '$email', '$dni', '$telefono', '$contraseña')";

if ($conn->query($sql) === TRUE) {
    echo "Nuevo registro creado con éxito";
    header("Location: login_v2.php"); // Redirigir al login después de registrarse
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
