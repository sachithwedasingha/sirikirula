<?php

include_once('../../function/expenseFunction.php');

$obj=new Expense();

echo $obj->loadTodayExpenses();