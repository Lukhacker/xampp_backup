<?php
// Este archivo solo procesará el formulario cuando se reciba una solicitud AJAX

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax'])) {
    // Obtener los valores enviados por el formulario
    $area = $_POST["area"];
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $mensaje = $_POST["mensaje"];

    // Direcciones de correo según el área seleccionada
    $correos = [
        "ayuntamiento" => "ayuntamiento@ejemplo.com",
        "mayores" => "mayores@ejemplo.com",
        "juventud" => "juventud@ejemplo.com",
        "cultura" => "cultura@ejemplo.com"
    ];

    // Seleccionar el correo correspondiente según el área
    $destinatario = $correos[$area] ?? "info@ejemplo.com"; // Correo por defecto
    $asunto = "Consulta de $nombre";
    $cuerpo = "De: $nombre\nEmail: $email\n\nMensaje:\n$mensaje";

    // Configurar los encabezados del correo
    $headers = "From: $email\r\nReply-To: $email\r\n";

    // Intentar enviar el correo
    if (mail($destinatario, $asunto, $cuerpo, $headers)) {
        echo json_encode(["status" => "success", "message" => "¡Gracias! Tu mensaje ha sido enviado correctamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Hubo un problema al enviar tu mensaje. Por favor, intenta nuevamente."]);
    }

    exit();
}
?>
