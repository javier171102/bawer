<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Recibir los datos del formulario
    $nombre_testigo = $_POST['nombre_testigo'];
    $dni_testigo = $_POST['dni_testigo'];
    $tipo_documento = $_POST['tipo_documento'];
    $observaciones = $_POST['observaciones'];

    // Verificar si el archivo del documento de legalización está disponible
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        // Obtener el nombre del archivo y la extensión
        $documento_name = $_FILES['documento']['name'];
        $documento_tmp = $_FILES['documento']['tmp_name'];
        $documento_ext = strtolower(pathinfo($documento_name, PATHINFO_EXTENSION));

        // Generar nombre único para el archivo
        $documento_path = 'uploads/legalizaciones/' . uniqid() . '.' . $documento_ext;

        // Mover el archivo a la carpeta de destino
        if (!move_uploaded_file($documento_tmp, $documento_path)) {
            echo "Error al subir el documento de legalización.";
            exit;
        }
    } else {
        echo "Debe subir el documento de legalización.";
        exit;
    }

    // Verificar si el archivo de la firma del testigo está disponible
    if (isset($_FILES['firma_testigo']) && $_FILES['firma_testigo']['error'] == 0) {
        // Obtener el nombre del archivo y la extensión
        $firma_name = $_FILES['firma_testigo']['name'];
        $firma_tmp = $_FILES['firma_testigo']['tmp_name'];
        $firma_ext = strtolower(pathinfo($firma_name, PATHINFO_EXTENSION));

        // Generar nombre único para el archivo
        $firma_path = 'uploads/firmas/' . uniqid() . '.' . $firma_ext;

        // Mover el archivo a la carpeta de destino
        if (!move_uploaded_file($firma_tmp, $firma_path)) {
            echo "Error al subir la firma del testigo.";
            exit;
        }
    } else {
        echo "Debe subir la firma del testigo.";
        exit;
    }

    // Conexión a la base de datos
    include 'db_connection.php';

    // Insertar los datos en la base de datos
    $sql = "INSERT INTO legalizaciones (nombre_testigo, dni_testigo, tipo_documento, documento, firma_testigo, observaciones) 
            VALUES ('$nombre_testigo', '$dni_testigo', '$tipo_documento', '$documento_path', '$firma_path', '$observaciones')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Legalización registrada correctamente.');
                window.location.href = 'legalizacion_firmas.html'; // Redirigir de nuevo al formulario
                </script>";
    } else {
        echo "Error al registrar la legalización: " . mysqli_error($conn);
    }

    // Cerrar la conexión
    mysqli_close($conn);
}
?>
