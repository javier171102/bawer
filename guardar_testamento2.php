<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "notaria_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Guardar datos
    $solicitanteNombre = $_POST['solicitante-nombre'];
    $solicitanteDni = $_POST['solicitante-dni'];
    $solicitanteTelefono = $_POST['solicitante-telefono'];
    $solicitanteDireccion = $_POST['solicitante-direccion'];
    $fallecidoNombre = $_POST['fallecido-nombre'];
    $fallecidoDni = $_POST['fallecido-dni'];
    $fechaFallecimiento = $_POST['fecha-fallecimiento'];
    $tipoTestamento = $_POST['tipo-testamento'];
    $observaciones = $_POST['observaciones'];

    $sql = "INSERT INTO apertura_testamento (solicitante_nombre, solicitante_dni, solicitante_telefono, 
        solicitante_direccion, fallecido_nombre, fallecido_dni, fecha_fallecimiento, tipo_testamento, observaciones) 
        VALUES ('$solicitanteNombre', '$solicitanteDni', '$solicitanteTelefono', '$solicitanteDireccion', 
                '$fallecidoNombre', '$fallecidoDni', '$fechaFallecimiento', '$tipoTestamento', '$observaciones')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Datos guardados correctamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al guardar los datos: " . $conn->error]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Obtener datos
    $sql = "SELECT * FROM apertura_testamento";
    $result = $conn->query($sql);

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
}

$conn->close();
?>
