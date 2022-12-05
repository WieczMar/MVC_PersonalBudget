<?php

namespace App\Controllers;

use \App\Auth;

//Signout controller
class Signout extends \Core\Controller
{
    //Log out a user
    public function indexAction()
    {
        Auth::logout();

        $this->redirect('/home/index');
    }
}
