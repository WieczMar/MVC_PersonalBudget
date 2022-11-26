<?php

namespace App\Controllers;

use \Core\View;

//Home controller
class Home extends \Core\Controller
{
    //Before filter
    protected function before()
    {}

    //After filter
    protected function after()
    {}

    //Show the index page
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
