<?php

namespace App\Http\Controllers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public static function send($para,$assunto,$view,$contentView){
        //CONFIGURAÇÕES DE SERVIDOR
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'maxhenrique308@gmail.com';                     //SMTP username
        $mail->Password   = 'xqkdmpvgokcqahrr';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465; 
        //DESTINATÁRIO
        $mail->setFrom('frtecnologiadigital@gmail.com', 'FR Academy'); //Rementente
        $mail->addAddress($para, 'Inscrito');     //Destinatário
        //Corpo
        $mail->isHTML(true);  // Seta o formato do e-mail para aceitar conteúdo HTML
        $mail->Subject = $assunto;
        $mail->Body = view($view,$contentView);
        $mail->send();
        //
    }
}
