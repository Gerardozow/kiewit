<?php
require_once('./includes/load.php');

require_once('./includes/libs/phpmailer/Exception.php');
require_once('./includes/libs/phpmailer/PHPMailer.php');
require_once('./includes/libs/phpmailer/SMTP.php');

//Cargar PHP MAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;





if (isset($_POST['reset_password'])) {

    $req_fields = array('forgotPassword');
    validate_fields($req_fields);

    $email = remove_junk($_POST['forgotPassword']);

    $id = find_by_email($email);

    if (!empty($id)) {
        //verificar si existe token activo y eliminarlo



        // Generar un token único
        $token = bin2hex(random_bytes(32));
        // Calcular la fecha de expiración (por ejemplo, en una hora)
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
        echo $expiration;
        recover_password($id['id'], $token, $expiration);


        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.hostinger.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'no-reply@gerardozow.me';                     //SMTP username
            $mail->Password   = 'z0wrceGer!!_';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('no-reply@gerardozow.me', 'Administracion Kiewit');
            $mail->addAddress($email);     //Add a recipient
            $mail->CharSet = 'UTF-8';

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Restablecimiento de contraseña';
            $mail->Body    = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Restablecimiento de Contraseña</title>
            </head>
            <body>                
                <p>Recientemente has solicitado restablecer la contraseña de tu cuenta en nuestro sitio web. Para continuar con este proceso, haz clic en el enlace a continuación:</p>
                
                <p><a href="http://localhost/kiewit/reset_password.php?token=' . $token . '">Restablece tu contraseña aquí</a></p>
                
                <p>Si no realizaste esta solicitud, puedes ignorar este correo. Tu contraseña actual seguirá siendo válida.</p>
                
                <p>Gracias por utilizar nuestros servicios.</p>
                
                <p>Atentamente,</p>
                
                <p>Sitema de Administracion kiewit</p>
            </body>
            </html>';
            $mail->AltBody = 'Restablece tu contraseña en el siguiente enlace <a href="http://localhost/kiewit/reset_password.php?token=' . $token . '">Restablece tu contraseña aquí</a>';

            $mail->send();
            $session->msg("s", 'si el email es correcto, se enviaran instrucciones para restablecer la contraseña');
            redirect('./index.php', false);
            //echo 'Message has been sent';
        } catch (Exception $e) {
            //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $session->msg("s", 'sAlgo salio mal, vuelve a intentarlo si persiste el error contacta al administrador');
            redirect('./index.php', false);
        }
    } else {
        echo 'id vacio';
        $session->msg("s", 'Si el email es correcto, se enviaran instrucciones para restablecer la contraseña');
        redirect('./index.php', false);
    }
} else {
    redirect('./index.php', false);
}
