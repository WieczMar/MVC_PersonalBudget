<?php

namespace App;

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
        $to = $email;
        $from = 'Personal Budget <no-reply@personal-budget.com';

        $headers  = 'MIME-Version: 1.0'."\r\n";
        $headers .= 'Content-Type: text/html; charset=utf-8'."\r\n";
        $headers .= 'From: '.$from."\r\n";
    
        mail($to, $subject, $message, $headers);
    }

}
