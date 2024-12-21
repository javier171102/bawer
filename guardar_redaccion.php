<?php
// Incluir la conexión a la base de datos
include 'db_connection.php';

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Recibir los datos del formulario
    $nombre_solicitante = $_POST['nombre_solicitante'];
    $tipo_escritura = $_POST['tipo_escritura'];
    $descripcion = $_POST['descripcion'];
    $observaciones = $_POST['observaciones'];

    // Verificar si el archivo del documento de identidad está disponible y no tiene errores
    if (isset($_FILES['documento_identidad']) && $_FILES['documento_identidad']['error'] == 0) {
        $documento_identidad_name = $_FILES['documento_identidad']['name'];
        $documento_identidad_tmp = $_FILES['documento_identidad']['tmp_name'];
        $documento_identidad_ext = strtolower(pathinfo($documento_identidad_name, PATHINFO_EXTENSION));
        $documento_identidad_path = 'uploads/redaccion_escrituras/' . uniqid() . '.' . $documento_identidad_ext;

        // Mover el archivo del documento de identidad
        if (move_uploaded_file($documento_identidad_tmp, $documento_identidad_path)) {

            // Verificar si el archivo adicional está disponible y no tiene errores
            $documento_adicional_path = NULL;
            if (isset($_FILES['documento_adicional']) && $_FILES['documento_adicional']['error'] == 0) {
                $documento_adicional_name = $_FILES['documento_adicional']['name'];
                $documento_adicional_tmp = $_FILES['documento_adicional']['tmp_name'];
                $documento_adicional_ext = strtolower(pathinfo($documento_adicional_name, PATHINFO_EXTENSION));
                $documento_adicional_path = 'uploads/redaccion_escrituras/' . uniqid() . '.' . $documento_adicional_ext;
                move_uploaded_file($documento_adicional_tmp, $documento_adicional_path);
            }

            // Insertar los datos en la base de datos
            $sql = "INSERT INTO redacciones_escrituras (nombre_solicitante, tipo_escritura, descripcion, documento_identidad, documento_adicional, observaciones) 
                    VALUES ('$nombre_solicitante', '$tipo_escritura', '$descripcion', '$documento_identidad_path', '$documento_adicional_path', '$observaciones')";

            if (mysqli_query($conn, $sql)) {
                echo "<script>
                        alert('Escritura redactada correctamente.');
                        window.location.href = 'redaccion_escrituras.html';
                        </script>";
            } else {
                echo "Error al registrar la redacción: " . mysqli_error($conn);
            }

        } else {
            echo "Error al subir el documento de identidad.";
        }
    } else {
        echo "Debe subir el documento de identidad.";
    }

    // Cerrar la conexión
    mysqli_close($conn);
}
?>
