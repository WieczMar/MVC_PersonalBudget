<?php

namespace App;

use App\Models\User;

class Auth
{
    // Login the user
    public static function login($user)
    {
        session_regenerate_id(true);

        $_SESSION['userId'] = $user->id;
        $_SESSION['username'] = $user->username; 
    }

    //Logout the user
    public static function logout()
    {
      // Unset all of the session variables
      session_unset(); 

      // Delete the session cookie
      if (ini_get('session.use_cookies')) {
          $params = session_get_cookie_params();

          setcookie(
              session_name(),
              '',
              time() - 42000,
              $params['path'],
              $params['domain'],
              $params['secure'],
              $params['httponly']
          );
      }

      // Finally destroy the session
      session_destroy();
    }

    //Remember the originally-requested page in the session
    public static function rememberRequestedPage()
    {
        $_SESSION['returnTo'] = $_SERVER['REQUEST_URI'];
    }

    // Get the originally-requested page to return to after requiring login, or default to the homepage
    public static function getReturnToPage()
    {
        return $_SESSION['returnTo'] ?? '/home/index';
    }

    // Get the current logged-in user, from the session or the remember-me cookie
    public static function getUser()
    {
        if (isset($_SESSION['userId'])) {

            return User::getByID($_SESSION['userId']);
        }
    }
}
