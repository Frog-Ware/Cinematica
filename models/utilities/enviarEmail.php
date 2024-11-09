<?php

// Envia un email sobre un asunto específico

require_once '../../vendor/autoload.php';
require_once '../../models/files/pdf.php';
require_once '../../models/db/traer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function enviarEmail($destinatario, $asunto, $datos)
{
    $mail = new PHPMailer(true);
    try {
        //Configuración del servidor usando SMTP.
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Setea el remitente.
        $rem = traerEmpresa();
        $mail->Username = $rem['email'];
        $mail->Password = $rem['passwd'];
        $mail->setFrom($rem['email'], $rem['nombreEmpresa']);
        $mail->addReplyTo($rem['email'], $rem['nombreEmpresa']);

        // Setea el port.
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        // Setea el destinatario.
        $mail->addAddress($destinatario);

        // Setea el contenido.
        $contenido = traerMail($asunto);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Subject = $contenido['cabecera'];
        foreach ($datos as $k => $v)
            if (!is_array($v))
                $contenido['cuerpo'] = str_replace("@$k", $v, $contenido['cuerpo']);
        $mail->Body = $contenido['cuerpo'];
        if ($asunto == 'Compra')
            $mail->addStringAttachment(generarPDF($datos), 'factura.pdf');

        // Envia el mail
        print_r(strlen(generarPDF($datos)));
        $mail->send();
        return true;
    } catch (Exception $e) {
        print_r($e);
        return false;
    }
}