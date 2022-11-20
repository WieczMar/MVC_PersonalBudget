<?php

namespace App\Controllers;

use \Core\View;

//Signup controller
class Signout extends \Core\Controller
{

    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        //echo "(before) ";
        //return false;
    }

    /**
     * After filter
     *
     * @return void
     */
    protected function after()
    {
        //echo " (after)";
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        session_start();
        session_unset(); 
        header('Location: /home/index');
    }
}
