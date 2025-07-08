<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Validar el correo
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Generar un código aleatorio de 5 dígitos
        $verification_code = rand(10000, 99999);

        // Guardar el código en la sesión
        $_SESSION['verification_code'] = $verification_code;

        // Incluir la librería de PHPMailer
        require('phpmailer/PHPMailerAutoload.php');

        

        $mail = new PHPMailer;

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->SMTPDebug=0;
            $mail->Debugoutput='html';
            $mail->Host='mail.winston93.edu.mx';
            $mail->Port=25; 
            $mail->SMTPAuth=true;
            $mail->isHTML(true);
            $mail->Username='avisos@winston93.edu.mx';
            $mail->Password='avisos#winston#2018';
            $mail->setFrom("avisos@winston93.edu.mx","Instituto Winston Churchill AC");

            // Remitente y destinatario
            $mail->setFrom('isc.escobedo@gmail.com', 'Instituto Winston Churchill');
            $mail->addAddress($email);

            // Contenido del mensaje
            $mail->isHTML(true);
            $mail->Subject = mb_encode_mimeheader('Código de Verificación', 'UTF-8');
            $mail->Body    = "Tu código de verificación es: <b>$verification_code</b>";

            // Enviar el correo
            if ($mail->send()) {
                echo "Código enviado con éxito al correo. Ahora, ingrésalo en la siguiente página.";
                echo '<br><a href="verify_code.html">Ir a verificar código</a>';
            } else {
                echo "Error al enviar el correo. Inténtalo de nuevo.";
            }
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } else {
        echo "Correo electrónico no válido.";
    }
} else {
    header('Location: login.html');
}
?>
