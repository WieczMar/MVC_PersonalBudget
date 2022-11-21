<?php

namespace App\Controllers;

use \Core\View;

//Signup controller
class Signout extends \Core\Controller
{
    //Before filter
    protected function before()
    {
        session_start();
    }

    //After filter
    protected function after()
    {

    }

    //Show the index page
    public function indexAction()
    {
        session_unset(); 
        header('Location: /home/index');
    }
}
