<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense as ExpenseModel;
use \App\Flash;

//Signin controller
class Expense extends Authenticated
{
    //Show the index page
    public function indexAction()
    {
        $rowsExpenseCategory = ExpenseModel::getExpenseCategories($_SESSION['userId']);
        $rowsPaymentMethod = ExpenseModel::getPaymentMethods($_SESSION['userId']);

        View::renderTemplate('Expense/index.html', [
            'rowsExpenseCategory' => $rowsExpenseCategory,
            'rowsPaymentMethod' => $rowsPaymentMethod
        ]);
    }

    public function addAction()
    {
        if ((isset($_POST['amount']))&&(!empty($_POST['amount']))) 
        {
            $expense = new ExpenseModel($_POST);

            if($expense->addNewExpense($_SESSION['userId'])){

                Flash::addMessage('savingTransactionResult' , 'You have successfully added new expense!', Flash::SUCCESS);
            }

        } else {

            Flash::addMessage('savingTransactionResult' , 'Error! Please fill in all required fields', Flash::WARNING);
        }
        $this->redirect('/expense/index');
    }       

}
