<?php
// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Recibir los datos del formulario
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $tipo_testamento = $_POST['tipo_testamento'];
    $observaciones = $_POST['observaciones'];

    // Verificar si los campos obligatorios están completos
    if (empty($nombre) || empty($dni) || empty($tipo_testamento) || !isset($_FILES['testamento']) || $_FILES['testamento']['error'] != 0) {
        echo "Por favor, complete todos los campos correctamente.";
        exit;
    }

    // Verificar si el archivo del testamento está disponible y no tiene errores
    if (isset($_FILES['testamento']) && $_FILES['testamento']['error'] == 0) {

        // Obtener el nombre del archivo y la extensión
        $testamento_name = $_FILES['testamento']['name'];
        $testamento_tmp = $_FILES['testamento']['tmp_name'];
        $testamento_ext = strtolower(pathinfo($testamento_name, PATHINFO_EXTENSION));

        // Generar nombre único para el archivo
        $testamento_path = 'uploads/testamentos/' . uniqid() . '.' . $testamento_ext;

        // Validación de la extensión del archivo (solo permitir PDF y DOCX)
        $allowed_extensions = ['pdf', 'doc', 'docx'];
        if (!in_array($testamento_ext, $allowed_extensions)) {
            echo "El archivo debe ser PDF o DOCX.";
            exit;
        }

        // Mover el archivo a la carpeta de destino
        if (move_uploaded_file($testamento_tmp, $testamento_path)) {

            // Conexión a la base de datos
            include 'db_connection.php';

            // Usar prepared statements para prevenir inyecciones SQL
            $stmt = $conn->prepare("INSERT INTO testamentos (nombre, dni, tipo_testamento, testamento, observaciones) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nombre, $dni, $tipo_testamento, $testamento_path, $observaciones);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Mostrar un mensaje de éxito y redirigir al formulario
                echo "<script>
                        alert('Testamento autenticado correctamente.');
                        window.location.href = 'autenticacion_testamentos.html';
                        </script>";
            } else {
                echo "Error al registrar el testamento: " . $stmt->error;
            }

            // Cerrar la conexión y la sentencia preparada
            $stmt->close();
            mysqli_close($conn);
        } else {
            echo "Error al subir el archivo.";
        }
    } else {
        echo "Debe subir el testamento.";
    }
}
?>
