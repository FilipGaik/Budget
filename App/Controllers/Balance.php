<?php

namespace App\Controllers;

use PDO;
use \Core\View;
use \App\Models\BalanceModel;
use \App\Auth;
use \App\Flash;

/**
 * Balance controller
 * 
 * PHP version 7.0
 */
class Balance extends Authenticated {
  /**
   * Before filter - called before each action method
   * 
   * @return void
   */
  protected function before() {
    parent::before();
    
    $this->user = Auth::getUser();
  }

  /**
   * Show balance page
   * 
   * @return void
   */
  public function showAction() {
    View::renderTemplate('Balance/show.html', ['user' => $this->user]);
  }

  /**
   * Show selected period balance
   * 
   * @return void
   */
  public function showBalance() {
    $period = $_POST['period'];

    if($period == 'current-month') {

      View::renderTemplate('Balance/show.html', [
        'period' => $period, 
        'incomesSum' => BalanceModel::getIncomesSum($_SESSION['user_id'], date('Y-m-01'), date('Y-m-t')), 
        'incomes' => BalanceModel::getIncomes($_SESSION['user_id'], date('Y-m-01'), date('Y-m-t')), 
        'expensesSum' => BalanceModel::getExpensesSum($_SESSION['user_id'], date('Y-m-01'), date('Y-m-t')), 
        'expenses' => BalanceModel::getExpenses($_SESSION['user_id'], date('Y-m-01'), date('Y-m-t')), 
        'balance' => $this->calculateBalance(date('Y-m-01'), date('Y-m-t')), 
        'incomesCategories' => BalanceModel::getIncomesCategories($_SESSION['user_id']), 
        'expensesCategories' => BalanceModel::getExpensesCategories($_SESSION['user_id']), 
        'payment' => BalanceModel::getPaymentMethodsAssignedToUser($_SESSION['user_id'])
      ]);

    } elseif($period == 'previous-month') {

      View::renderTemplate('Balance/show.html', [
        'period' => $period, 
        'incomesSum' => BalanceModel::getIncomesSum($_SESSION['user_id'], date('Y-m-01', strtotime(date('Y-m')." -1 month")), date('Y-m-t', strtotime(date('Y-m')." -1 month"))), 
        'incomes' => BalanceModel::getIncomes($_SESSION['user_id'], date('Y-m-01', strtotime(date('Y-m')." -1 month")), date('Y-m-t', strtotime(date('Y-m')." -1 month"))), 
        'expensesSum' => BalanceModel::getExpensesSum($_SESSION['user_id'], date('Y-m-01', strtotime(date('Y-m')." -1 month")), date('Y-m-t', strtotime(date('Y-m')." -1 month"))), 
        'expenses' => BalanceModel::getExpenses($_SESSION['user_id'], date('Y-m-01', strtotime(date('Y-m')." -1 month")), date('Y-m-t', strtotime(date('Y-m')." -1 month"))), 
        'balance' => $this->calculateBalance(date('Y-m-01', strtotime(date('Y-m')." -1 month")), date('Y-m-t', strtotime(date('Y-m')." -1 month"))), 
        'incomesCategories' => BalanceModel::getIncomesCategories($_SESSION['user_id']), 
        'expensesCategories' => BalanceModel::getExpensesCategories($_SESSION['user_id']), 
        'payment' => BalanceModel::getPaymentMethodsAssignedToUser($_SESSION['user_id'])
      ]);

    } elseif($period == 'current-year') {

      View::renderTemplate('Balance/show.html', [
        'period' => $period, 
        'incomesSum' => BalanceModel::getIncomesSum($_SESSION['user_id'], date('Y-01-01'), date('Y-12-31')), 
        'incomes' => BalanceModel::getIncomes($_SESSION['user_id'], date('Y-01-01'), date('Y-12-31')), 
        'expensesSum' => BalanceModel::getExpensesSum($_SESSION['user_id'], date('Y-01-01'), date('Y-12-31')), 
        'expenses' => BalanceModel::getExpenses($_SESSION['user_id'], date('Y-01-01'), date('Y-12-31')), 
        'balance' => $this->calculateBalance(date('Y-01-01'), date('Y-12-31')), 
        'incomesCategories' => BalanceModel::getIncomesCategories($_SESSION['user_id']), 
        'expensesCategories' => BalanceModel::getExpensesCategories($_SESSION['user_id']), 
        'payment' => BalanceModel::getPaymentMethodsAssignedToUser($_SESSION['user_id'])
      ]);

    } else {

      View::renderTemplate('Balance/show.html', [
        'period' => $period, 
        'incomesSum' => BalanceModel::getIncomesSum($_SESSION['user_id'], $_POST['from'], $_POST['to']), 
        'incomes' => BalanceModel::getIncomes($_SESSION['user_id'], $_POST['from'], $_POST['to']), 
        'expensesSum' => BalanceModel::getExpensesSum($_SESSION['user_id'], $_POST['from'], $_POST['to']), 
        'expenses' => BalanceModel::getExpenses($_SESSION['user_id'], $_POST['from'], $_POST['to']), 
        'balance' => $this->calculateBalance($_POST['from'], $_POST['to']), 
        'from' => $_POST['from'], 
        'to' => $_POST['to'], 
        'incomesCategories' => BalanceModel::getIncomesCategories($_SESSION['user_id']), 
        'expensesCategories' => BalanceModel::getExpensesCategories($_SESSION['user_id']), 
        'payment' => BalanceModel::getPaymentMethodsAssignedToUser($_SESSION['user_id'])
      ]);
    }
  }

  /**
   * Save edited income
   * 
   * @return void
   */
  public function saveEditedIncomeAction() {
    if($_POST['number'] == NULL || $_POST['number'] <= 0) {

      Flash::addMessage('Income was not updated, please try again', Flash::WARNING);

    } else {

      if(BalanceModel::saveIncome($_POST['category'], $_POST['number'], $_POST['date'], $_POST['income_id'], $_POST['comment'])) {

        Flash::addMessage('Income updated');

      } else {

        Flash::addMessage('Income was not updated, please try again', Flash::WARNING);
     }
    }
    
    $this->showBalance();
  }

  /**
   * Save edited expense
   * 
   * @return void
   */
  public function saveEditedExpenseAction() {
    if($_POST['number'] == NULL || $_POST['number'] <= 0) {

      Flash::addMessage('Expense was not updated, please try again', Flash::WARNING);

    } else {

      if(BalanceModel::saveExpense($_POST['category'], $_POST['payment'], $_POST['number'], $_POST['date'], $_POST['expense_id'], $_POST['comment'])) {

        Flash::addMessage('Expense updated');

      } else {

        Flash::addMessage('Expense was not updated, please try again', Flash::WARNING);
      }
    }
    
    $this->showBalance();
  }

  /**
   * Delete income
   * 
   * @return void
   */
  public function deleteIncomeAction() {
    BalanceModel::deleteIncome($_POST['income_id']);
    
    Flash::addMessage('Income deleted');

    $this->showBalance();
  }

  /**
   * Delete expense
   * 
   * @return void
   */
  public function deleteExpenseAction() {
    BalanceModel::deleteExpense($_POST['expense_id']);

    Flash::addMessage('Expense deleted');

    $this->showBalance();
  }

  /**
   * Calculate balance
   * @param date $startDate Start date
   * @param date $endDate End date
   * 
   * @return float Balance
   */
  public function calculateBalance($startDate, $endDate) {
    $incomesSumArr = BalanceModel::getIncomesSum($_SESSION['user_id'], $startDate, $endDate);
    $expensesSumArr = BalanceModel::getExpensesSum($_SESSION['user_id'], $startDate, $endDate);

    $incomesSum = 0;
    $expensesSum = 0;

    foreach($incomesSumArr as $catSum) {
      $incomesSum += $catSum[0];
    }
    
    foreach($expensesSumArr as $catSum) {
      $expensesSum += $catSum[0];
    }

    return $incomesSum - $expensesSum;
  }
}
