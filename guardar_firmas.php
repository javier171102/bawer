<?php
// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar que el archivo de la firma está disponible y no tiene errores
    if (isset($_FILES['firma']) && $_FILES['firma']['error'] == 0) {
        // Obtener el nombre del archivo y la extensión de la firma
        $firma_name = $_FILES['firma']['name'];
        $firma_tmp = $_FILES['firma']['tmp_name'];
        $firma_ext = strtolower(pathinfo($firma_name, PATHINFO_EXTENSION));

        // Validar si la extensión del archivo es permitida (solo imágenes)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($firma_ext, $allowed_extensions)) {
            echo json_encode(['message' => 'Error: Solo se permiten imágenes (JPG, JPEG, PNG, GIF).']);
            exit;
        }

        // Crear un nombre único para la firma
        $firma_new_name = uniqid() . '.' . $firma_ext;

        // Ruta completa para guardar el archivo
        $upload_dir = __DIR__ . '/uploads/firmas/';
        $firma_dest = $upload_dir . $firma_new_name;

        // Intentar mover el archivo a la carpeta de destino
        if (move_uploaded_file($firma_tmp, $firma_dest)) {

            // Guardar la información en la base de datos
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "notaria_db";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            // Recoger los datos del formulario
            $nombre = $_POST['nombre'];
            $dni = $_POST['dni'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $documento_name = $_FILES['documento']['name'];
            $notas = $_POST['notas'];

            // Mover el archivo del documento
            if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
                $documento_tmp = $_FILES['documento']['tmp_name'];
                $documento_ext = strtolower(pathinfo($documento_name, PATHINFO_EXTENSION));
                $allowed_docs = ['pdf', 'doc', 'docx'];

                if (in_array($documento_ext, $allowed_docs)) {
                    $documento_new_name = uniqid() . '.' . $documento_ext;
                    $documento_dest = __DIR__ . '/uploads/documentos/' . $documento_new_name;
                    move_uploaded_file($documento_tmp, $documento_dest);
                }
            }

            // Consulta SQL para insertar los datos en la base de datos
            $sql = "INSERT INTO autenticacion_firmas (nombre, dni, direccion, telefono, firma, documento, notas)
                    VALUES ('$nombre', '$dni', '$direccion', '$telefono', '$firma_dest', '$documento_dest', '$notas')";

            if ($conn->query($sql) === TRUE) {
                // Respuesta de éxito
                echo json_encode(['message' => 'Guardado correctamente']);
            } else {
                // Enviar mensaje de error si hay un problema al guardar
                echo json_encode(['message' => 'Error al guardar en la base de datos: ' . $conn->error]);
            }

            $conn->close();
        } else {
            echo json_encode(['message' => 'Error al subir la firma. Verifique los permisos de la carpeta.']);
        }
    } else {
        echo json_encode(['message' => 'No se seleccionó ninguna firma o hubo un error en la carga del archivo.']);
    }
} else {
    // Cargar los datos al acceder a la página o al enviar GET
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "notaria_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta para obtener los registros de la base de datos
    $sql = "SELECT * FROM autenticacion_firmas";
    $result = $conn->query($sql);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Enviar los datos en formato JSON
    echo json_encode($data);

    $conn->close();
}
