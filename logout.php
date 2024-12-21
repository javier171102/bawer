<?php
session_start(); // Iniciar la sesión
session_unset(); // Eliminar todas las variables de sesión
session_destroy(); // Destruir la sesión
header('Location: index.html'); // Redirigir al formulario de inicio de sesión
exit();
?>
