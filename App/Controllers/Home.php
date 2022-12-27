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
        if (isset($_SESSION['userId'])) 
        {
            View::renderTemplate('Home/home.html');
        }
        else{
            View::renderTemplate('Home/index.html');
        }

    }
}
