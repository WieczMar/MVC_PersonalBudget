<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balance as BalanceModel;
//Signin controller
class Balance extends \Core\Controller
{
    protected function before()
    {
        session_start();
    }

    protected function after()
    {
        //echo " (after)";
    }

    public function indexAction()
    {
        if ((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn']==true))
        {
            if (!isset($_POST['selectedPeriod'])) $selectedPeriod = "Current month"; // first entry on balance page after logging
            else $selectedPeriod = $_POST['selectedPeriod'];

            $periods = array('Current month', 'Previous month', 'Current year', 'Nonstandard');
            // getting current date
            $currentMonth = date('m');
            $currentYear = date('Y');
            $previousMonth = sprintf("%02d", $currentMonth-1);

            switch ($selectedPeriod) {
                case 'Current month':
                    $startDate = $currentYear.'-'.$currentMonth.'-01';
                    $endDate = $currentYear.'-'.$currentMonth.'-'.cal_days_in_month(CAL_GREGORIAN,$currentMonth,$currentYear);
                    break;
                case 'Previous month':
                    $startDate = $currentYear.'-'.$previousMonth.'-01';
                    $endDate = $currentYear.'-'.$previousMonth.'-'.cal_days_in_month(CAL_GREGORIAN,$previousMonth,$currentYear);
                    break;
                case 'Current year':
                    $startDate = $currentYear.'-01-01';
                    $endDate = $currentYear.'-12-31';
                    break;
                case 'Nonstandard':
                    $startDate = $_POST['startDate'];
                    $endDate = $_POST['endDate'];
                    if($startDate > $endDate) $_SESSION["wrongDateRange"] = true;
                    break;
            }

            // get incomes data from database
            $rowsIncomes = BalanceModel::getIncomes($_SESSION['userId'], $startDate, $endDate);
            if(!empty($rowsIncomes))
            {
                $sumOfIncomes = number_format(array_sum(array_column($rowsIncomes, 'categoryAmount')), 2,  '.', ''); // aggregate incomes amount with 2 digit precision
            }
            else { // null handling case
                $rowsIncomes = array(array('categoryName' => '-', 'categoryAmount' => '-'));
                $sumOfIncomes = number_format(0, 2,  '.', '');
            }	

            // get expenses data from database
            $rowsExpenses = BalanceModel::getExpenses($_SESSION['userId'], $startDate, $endDate);
            if(!empty($rowsExpenses))
            {
                $sumOfExpenses = number_format(array_sum(array_column($rowsExpenses, 'categoryAmount')), 2,  '.', ''); // aggregate expenses amount with 2 digit precision
            }
            else { // null handling case
                $rowsExpenses = array(array('categoryName' => '-', 'categoryAmount' => '-'));
                $sumOfExpenses = number_format(0, 2,  '.', '');
            }
            
            $balance = number_format($sumOfIncomes - $sumOfExpenses, 2,  '.', '');
            
            View::renderTemplate('Balance/index.html', [
                'wrongDateRange' => isset($_SESSION["wrongDateRange"]),
                'periods' => $periods,
                'selectedPeriod' => $selectedPeriod,
                'rowsIncomes' => $rowsIncomes,
                'rowsExpenses' => $rowsExpenses,
                'sumOfIncomes' => $sumOfIncomes,
                'sumOfExpenses' =>  $sumOfExpenses,
                'balance' => $balance
            ]);

            unset($_SESSION["wrongDateRange"]);

        }
        else{
            
            header('Location: /home/index');
            
        }
        
    }
}
