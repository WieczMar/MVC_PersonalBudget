<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//Mail controller
class Mail extends \Core\Controller
{
    //Before filter
    protected function before()
    {}

    //After filter
    protected function after()
    {}

    public static function send($email, $subject, $message)
    {
        ob_start();
        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;

        $mail->Username = \App\Config::MAIL_USERNAME; 
        $mail->Password = \App\Config::MAIL_PASSWORD; 

        $mail->CharSet = 'UTF-8';
        $mail->setFrom('no-reply@personal-budget.com', 'Personal Budget');
        $mail->addAddress($email);
        $mail->addReplyTo('marcin.wieczorek.programista@gmail.com', 'Admin');

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        ob_end_clean();
    }

}
