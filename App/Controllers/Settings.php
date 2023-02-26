<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense;
use \App\Models\Income;
use \App\Models\User;
use \App\Auth;

// Settings controller
class Settings extends Authenticated
{
    public function indexAction()
    {
        View::renderTemplate('Settings/index.html');  
    }

    public function getUsernameAction()
    {   
        echo json_encode(User::getUsername(), JSON_UNESCAPED_UNICODE);
    }

    public function editUsernameAction()
    {   
        $request_body = file_get_contents('php://input');
        $name = json_decode($request_body);
        
        if(!empty($name)){
            http_response_code(200);
            echo json_encode(User::editUsername($name), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(400);
            $error = "Name value cannot be empty.";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        }
    }

    public function editPasswordAction()
    {   
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        $userId = $_SESSION['userId'];
        $user = User::getByID($userId);

        if (password_verify($data->oldPassword, $user->password)) {
            $user->password = $data->newPassword;
            $user->validatePassword();
            if (empty($user->errors)) {
                http_response_code(200);
                echo json_encode(User::editPassword($user->password), JSON_UNESCAPED_UNICODE);  
            } else {
                http_response_code(400);
                $error = $user->errors['password'];
                echo json_encode($error, JSON_UNESCAPED_UNICODE);
            }       
        } else {
            http_response_code(400);
            $error = "Wrong old password value.";
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
        }
    }

    public function deleteAccountAction()
    {
        if(isset($_POST['delete']))
        {
            User::deleteUser();
            Auth::logout();
        }
        $this->redirect('/home/index');
        
    }
    
}
