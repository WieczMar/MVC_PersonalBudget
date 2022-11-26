<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense as ExpenseModel;
//Signin controller
class Expense extends \Core\Controller
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
            $rowsExpenseCategory = ExpenseModel::getExpenseCategories($_SESSION['userId']);
            $rowsPaymentMethod = ExpenseModel::getPaymentMethods($_SESSION['userId']);

            View::renderTemplate('Expense/index.html', [
                'savingTransactionCompleted' => isset($_SESSION['savingTransactionCompleted']),
                'rowsExpenseCategory' => $rowsExpenseCategory,
                'rowsPaymentMethod' => $rowsPaymentMethod
            ]);
            unset($_SESSION['savingTransactionCompleted']);
        }
        else{           
            $this->redirect('/home/index');
        }
    }

    public function addAction()
    {
        if ((isset($_POST['amount']))&&(!empty($_POST['amount']))) 
        {
            ExpenseModel::addNewExpense($_SESSION['userId'], $_POST['category'], $_POST['paymentMethod'], $_POST['amount'], $_POST['date'], $_POST['comment']);
            $_SESSION['savingTransactionCompleted'] = true;

        }
        header('Location: http://'.$_SERVER['HTTP_HOST'].'/expense/index');
    }       

}
