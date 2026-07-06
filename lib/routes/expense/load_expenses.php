<?php

include_once('../../function/expenseFunction.php');

$obj=new Expense();

echo $obj->loadExpenses(
    $_GET['from_date'],
    $_GET['to_date'],
    $_GET['expense_category'],
    $_GET['payment_method']
);