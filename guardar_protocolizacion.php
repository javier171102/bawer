<?php
// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Recibir los datos del formulario
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $tipo_acto = $_POST['tipo_acto'];
    $observaciones = $_POST['observaciones'];

    // Verificar si el archivo del documento está disponible y no tiene errores
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        // Obtener el nombre del archivo y la extensión
        $documento_name = $_FILES['documento']['name'];
        $documento_tmp = $_FILES['documento']['tmp_name'];
        $documento_ext = strtolower(pathinfo($documento_name, PATHINFO_EXTENSION));

        // Generar nombre único para el archivo
        $documento_path = 'uploads/protocolizacion/' . uniqid() . '.' . $documento_ext;

        // Validación de la extensión del archivo (solo permitir PDF y DOCX)
        $allowed_extensions = ['pdf', 'doc', 'docx'];
        if (!in_array($documento_ext, $allowed_extensions)) {
            echo "El archivo debe ser PDF o DOCX.";
            exit;
        }

        // Mover el archivo a la carpeta de destino
        if (move_uploaded_file($documento_tmp, $documento_path)) {

            // Conexión a la base de datos
            include 'db_connection.php';

            // Insertar los datos en la base de datos
            $sql = "INSERT INTO protocolizacion (nombre, dni, tipo_acto, documento, observaciones) 
                    VALUES ('$nombre', '$dni', '$tipo_acto', '$documento_path', '$observaciones')";

            if (mysqli_query($conn, $sql)) {
                echo "<script>
                        alert('Acto Jurídico protocolizado correctamente.');
                        window.location.href = 'protocolizacion_actos.html'; // Redirigir de nuevo al formulario
                        </script>";
            } else {
                echo "Error al registrar el acto jurídico: " . mysqli_error($conn);
            }

            // Cerrar la conexión
            mysqli_close($conn);
        } else {
            echo "Error al subir el archivo.";
        }
    } else {
        echo "Debe subir el documento.";
    }
}
?>
