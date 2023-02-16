<?php
//Front controller

//Composer
require '../vendor/autoload.php';

//Twig
Twig_Autoloader::register();

//Error and Exception handling
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

//Sessions
session_start();

//Routing
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}', ['action' => 'index']);
$router->add('{controller}/', ['action' => 'index']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);

$router->add('api/expense-limit/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'getLimitOfExpensesInCategory']);
$router->add('api/expense-sum/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'getSumOfExpensesInCategoryForSelectedMonth']);
$router->add('api/expense-details/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'getExpenseDetailsInCategoryForSelectedDates']);
$router->add('api/expense-category-name/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'getExpenseCategoryName']);

$router->add('api/income-details/{id:[\d]+}', ['controller' => 'Income', 'action' => 'getIncomeDetailsInCategoryForSelectedDates']);
$router->add('api/income-category-name/{id:[\d]+}', ['controller' => 'Income', 'action' => 'getIncomeCategoryName']);
//$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);
    
$router->dispatch($_SERVER['QUERY_STRING']);
