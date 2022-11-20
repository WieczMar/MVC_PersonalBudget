<?php

namespace App\Controllers;

use \Core\View;

//Home controller
class Home extends \Core\Controller
{

    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        session_start();
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
        if ((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn']==true))
        {
            View::renderTemplate('Home/home.html', [
                'username' => $_SESSION['username'],
            ]);
        }
        else{
            View::renderTemplate('Home/index.html');
        }

    }
}
