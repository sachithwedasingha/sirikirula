<?php

include_once('../../function/expenseFunction.php');

$obj=new Expense();

echo $obj->saveExpense(
    $_POST['expense_date'],
    $_POST['expense_category'],
    $_POST['payment_method'],
    $_POST['amount'],
    $_POST['description'],
    $_POST['createdby']
);