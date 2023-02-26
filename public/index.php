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
$router->add('api/income-details/{id:[\d]+}', ['controller' => 'Income', 'action' => 'getIncomeDetailsInCategoryForSelectedDates']);
$router->add('api/income-category-name/{id:[\d]+}', ['controller' => 'Income', 'action' => 'getIncomeCategoryName']);
$router->add('api/income-categories', ['controller' => 'Income', 'action' => 'getIncomeCategories']);

$router->add('api/income-category', ['controller' => 'Income', 'action' => 'editIncomeCategory']);
$router->add('api/income-category-new', ['controller' => 'Income', 'action' => 'addIncomeCategory']);

$router->add('api/income-category-dump/{id:[\d]+}', ['controller' => 'Income', 'action' => 'deleteIncomeCategory']);
$router->add('api/income-dump/{id:[\d]+}', ['controller' => 'Income', 'action' => 'deleteIncome']);

$router->add('api/expense-limit/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'getLimitOfExpensesInCategory']);
$router->add('api/expense-sum/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'getSumOfExpensesInCategoryForSelectedMonth']);
$router->add('api/expense-details/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'getExpenseDetailsInCategoryForSelectedDates']);
$router->add('api/expense-category-name/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'getExpenseCategoryName']);
$router->add('api/expense-categories', ['controller' => 'Expense', 'action' => 'getExpenseCategories']);
$router->add('api/expense-payment-methods', ['controller' => 'Expense', 'action' => 'getPaymentMethods']);

$router->add('api/expense-category', ['controller' => 'Expense', 'action' => 'editExpenseCategory']);
$router->add('api/expense-category-new', ['controller' => 'Expense', 'action' => 'addExpenseCategory']);
$router->add('api/expense-payment-method', ['controller' => 'Expense', 'action' => 'editPaymentMethod']);
$router->add('api/expense-payment-method-new', ['controller' => 'Expense', 'action' => 'addPaymentMethod']);

$router->add('api/expense-category-dump/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'deleteExpenseCategory']);
$router->add('api/expense-payment-method-dump/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'deletePaymentMethod']);
$router->add('api/expense-dump/{id:[\d]+}', ['controller' => 'Expense', 'action' => 'deleteExpense']);

$router->add('api/username', ['controller' => 'Settings', 'action' => 'getUsername']);
$router->add('api/settings/username', ['controller' => 'Settings', 'action' => 'editUsername']);
$router->add('api/settings/password', ['controller' => 'Settings', 'action' => 'editPassword']);

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}', ['action' => 'index']);
$router->add('{controller}/', ['action' => 'index']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('settings/delete-account', ['controller' => 'Settings', 'action' => 'deleteAccount']);

//$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);
    
$router->dispatch($_SERVER['QUERY_STRING']);
