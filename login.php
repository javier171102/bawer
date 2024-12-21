<?php
session_start(); // Iniciar sesión

// Datos de conexión a la base de datos
$servidor = 'localhost'; // Si tu base de datos está en el mismo servidor, 'localhost'
$usuario = 'root'; // El usuario por defecto en XAMPP es 'root'
$contrasena = ''; // Contraseña por defecto en XAMPP es una cadena vacía
$base_de_datos = 'notaria_db'; // El nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($servidor, $usuario, $contrasena, $base_de_datos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$email = $_POST['email'];
$contraseña = $_POST['contraseña']; // Cambié la variable a 'contraseña' porque en tu formulario la estás usando así

// Validar las credenciales
$sql = "SELECT * FROM usuarios WHERE email = ?"; // Seleccionar usuario por correo electrónico
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

// Verificar si el correo electrónico existe
if ($resultado->num_rows > 0) {
    // El correo existe, ahora verificar la contraseña
    $usuario = $resultado->fetch_assoc();
    
    // Verificar si la contraseña es correcta
    if (password_verify($contraseña, $usuario['contraseña'])) {
        // Contraseña correcta
        $_SESSION['alert'] = 'Inicio de sesión exitoso';
        header('Location: panel.html'); // Redirigir al panel
        $conn->close(); // Cerrar la conexión antes de hacer el exit
        exit();
    } else {
        // Contraseña incorrecta
        $_SESSION['alert'] = 'Correo o contraseña incorrectos';
        header('Location: index.html'); // Redirigir al formulario de login
        $conn->close(); // Cerrar la conexión antes de hacer el exit
        exit();
    }
} else {
    // Correo electrónico no encontrado
    $_SESSION['alert'] = 'Correo o contraseña incorrectos';
    header('Location: index.html'); // Redirigir al formulario de login
    $conn->close(); // Cerrar la conexión antes de hacer el exit
    exit();
}
?>
