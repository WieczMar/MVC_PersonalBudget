<?php

namespace App\Controllers;
//Mail controller
class Mail extends \Core\Controller
{
    //Before filter
    protected function before()
    {
    }

    //After filter
    protected function after()
    {
    }

    public static function sendSignupConfirmation($name, $email)
    {
        $to = $email;
        $from = 'Personal Budget <no-reply@personal-budget.com';
        $subject = 'Welcome to Personal Budget Website App!';
        $message = ' 
        <html>
        <head>
          <title>Thank you for signing up to Personal Budget Website!</title>
        </head>
        <body>
          <h1>Hello'.$name.'!</h1>
          <p>Oto link do naszego świetnego ebooka: <a href="https://domena.pl/ebook.pdf">POBIERZ EBOOKA</a>
          </p>
          <hr>
          <p>Regards,</p>
          <p>Admin Marcin from <a href="https://budget.marcin-wieczorek.profesjonalnyprogramista.pl">Personal Buget Website</a>
          </p>
        </body>
        </html>';

        $headers  = 'MIME-Version: 1.0'."\r\n";
        $headers .= 'Content-Type: text/html; charset=utf-8'."\r\n";
        $headers .= 'From: '.$from."\r\n";
    
        mail($to, $subject, $message, $headers);
    }

}
