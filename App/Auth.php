<?php

namespace App;

use App\Models\User;
use App\Models\RememberedLogin;

class Auth
{
    // Login the user
    public static function login($user, $rememberMe)
    {
        session_regenerate_id(true);

        $_SESSION['userId'] = $user->id;
        $_SESSION['username'] = $user->username; 

        if ($rememberMe) {

            if ($user->rememberLogin()) {

               setcookie('rememberMe', $user->rememberToken, $user->expiryTimestamp, '/');

            }
        }
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
      static::forgetLogin();
      
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
        } else {

            return static::loginFromRememberCookie();
        }
    }

    // Login the user from a remembered login cookie
    protected static function loginFromRememberCookie()
    {
        $cookie = $_COOKIE['rememberMe'] ?? false;

        if ($cookie) {

            $rememberedLogin = RememberedLogin::findByToken($cookie);

            if ($rememberedLogin && ! $rememberedLogin->hasExpired()) {

                $user = $rememberedLogin->getUser();

                static::login($user, false);

                return $user;
            }
        }
    }

    // Forget the remembered login, if present
    protected static function forgetLogin()
    {
        $cookie = $_COOKIE['rememberMe'] ?? false;

        if ($cookie) {

            $rememberedLogin = RememberedLogin::findByToken($cookie);

            if ($rememberedLogin) {

                $rememberedLogin->delete();

            }

            setcookie('rememberMe', '', time() - 3600);  // set to expire in the past
        }
    }
}
