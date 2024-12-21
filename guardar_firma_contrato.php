<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Recibir los datos del formulario
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $observaciones = $_POST['observaciones'];

    // Verificar si el archivo del documento del contrato está disponible
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        // Obtener el nombre del archivo y la extensión
        $documento_name = $_FILES['documento']['name'];
        $documento_tmp = $_FILES['documento']['tmp_name'];
        $documento_ext = strtolower(pathinfo($documento_name, PATHINFO_EXTENSION));

        // Generar nombre único para el archivo
        $documento_path = 'uploads/contratos/' . uniqid() . '.' . $documento_ext;

        // Mover el archivo a la carpeta de destino
        if (!move_uploaded_file($documento_tmp, $documento_path)) {
            echo "Error al subir el documento del contrato.";
            exit;
        }
    } else {
        echo "Debe subir el documento del contrato.";
        exit;
    }

    // Verificar si el archivo de la firma está disponible
    if (isset($_FILES['firma']) && $_FILES['firma']['error'] == 0) {
        // Obtener el nombre del archivo y la extensión
        $firma_name = $_FILES['firma']['name'];
        $firma_tmp = $_FILES['firma']['tmp_name'];
        $firma_ext = strtolower(pathinfo($firma_name, PATHINFO_EXTENSION));

        // Generar nombre único para el archivo
        $firma_path = 'uploads/firmas/' . uniqid() . '.' . $firma_ext;

        // Mover el archivo a la carpeta de destino
        if (!move_uploaded_file($firma_tmp, $firma_path)) {
            echo "Error al subir la firma.";
            exit;
        }
    } else {
        echo "Debe subir la firma del contratante.";
        exit;
    }

    // Conexión a la base de datos
    include 'db_connection.php';

    // Insertar los datos en la base de datos
    $sql = "INSERT INTO contratos (nombre, dni, tipo_contrato, documento, firma, observaciones) 
            VALUES ('$nombre', '$dni', '$tipo_contrato', '$documento_path', '$firma_path', '$observaciones')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Contrato firmado correctamente.');
                window.location.href = 'firma_contratos.html'; // Redirigir de nuevo al formulario
                </script>";
    } else {
        echo "Error al registrar el contrato: " . mysqli_error($conn);
    }

    // Cerrar la conexión
    mysqli_close($conn);
}
?>
