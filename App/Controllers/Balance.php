<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balance as BalanceModel;
use \App\Flash;

//Signin controller
class Balance extends Authenticated
{
    public function indexAction()
    {
        if (!isset($_POST['selectedPeriod'])) $selectedPeriod = "Current month"; // first entry on balance page after logging
        else $selectedPeriod = $_POST['selectedPeriod'];

        $periods = array('Current month', 'Previous month', 'Current year', 'Nonstandard');

        // get date range
        list($startDate, $endDate) = self::getDateRange($selectedPeriod);

        // get incomes data from database
        $rowsIncomes = BalanceModel::getIncomes($_SESSION['userId'], $startDate, $endDate);
        list($rowsIncomes, $sumOfIncomes) = self::formatAndAggregateBudgetData($rowsIncomes);

        // get expenses data from database
        $rowsExpenses = BalanceModel::getExpenses($_SESSION['userId'], $startDate, $endDate);
        list($rowsExpenses, $sumOfExpenses) = self::formatAndAggregateBudgetData($rowsExpenses);
        
        $balance = number_format($sumOfIncomes - $sumOfExpenses, 2,  '.', '');
        
        View::renderTemplate('Balance/index.html', [
            'periods' => $periods,
            'selectedPeriod' => $selectedPeriod,
            'rowsIncomes' => $rowsIncomes,
            'rowsExpenses' => $rowsExpenses,
            'sumOfIncomes' => $sumOfIncomes,
            'sumOfExpenses' =>  $sumOfExpenses,
            'balance' => $balance
        ]);  
    }

    private static function getDateRange($selectedPeriod)
    {
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
                if($startDate > $endDate) Flash::addMessage('incorrectDateRange' , 'Error! Selected incorrect date range!', Flash::WARNING);
                break;
        }

        return array($startDate, $endDate);
    }

    private static function formatAndAggregateBudgetData($rows)
    {
        if(!empty($rows))
        {
            $sum = number_format(array_sum(array_column($rows, 'categoryAmount')), 2,  '.', ''); // aggregate amount with 2 digit precision
        }
        else { // null handling case
            $rows = array(array('categoryName' => '-', 'categoryAmount' => '-'));
            $sum = number_format(0, 2,  '.', '');
        }

        return array($rows, $sum);
    }
}
