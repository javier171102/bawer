<?php
// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Recibir los datos del formulario
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $tipo_documento = $_POST['tipo_documento'];
    $observaciones = $_POST['observaciones'];

    // Verificar si el archivo del documento original está disponible y no tiene errores
    if (isset($_FILES['documento_original']) && $_FILES['documento_original']['error'] == 0) {
        // Obtener el nombre del archivo y la extensión del documento
        $documento_name = $_FILES['documento_original']['name'];
        $documento_tmp = $_FILES['documento_original']['tmp_name'];
        $documento_ext = strtolower(pathinfo($documento_name, PATHINFO_EXTENSION));

        // Generar nombre único para el archivo
        $documento_path = 'uploads/certificaciones/' . uniqid() . '.' . $documento_ext;

        // Mover el archivo a la carpeta de destino
        if (move_uploaded_file($documento_tmp, $documento_path)) {

            // Conexión a la base de datos
            include 'db_connection.php';

            // Insertar los datos en la base de datos
            $sql = "INSERT INTO certificaciones (nombre, dni, tipo_documento, documento_original, observaciones) 
                    VALUES ('$nombre', '$dni', '$tipo_documento', '$documento_path', '$observaciones')";

            if (mysqli_query($conn, $sql)) {
                echo "<script>
                        alert('Certificación registrada correctamente.');
                        document.getElementById('form').reset(); // Limpiar los campos del formulario
                        // No recargamos la página, solo restablecemos el formulario
                    </script>";
            } else {
                echo "<script>alert('Error al registrar la certificación: " . mysqli_error($conn) . "');</script>";
            }

            // Cerrar la conexión
            mysqli_close($conn);
        } else {
            echo "<script>alert('Error al subir el archivo.');</script>";
        }
    } else {
        echo "<script>alert('Debe subir el documento original.');</script>";
    }
}
?>
