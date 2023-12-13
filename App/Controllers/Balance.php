<?php

namespace App\Controllers;

use PDO;
use \Core\View;
use \App\Models\User;
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

      View::renderTemplate('Balance/show.html', ['period' => $period, 'incomesSum' => $this->getIncomesSum(date('Y-m-01'), date('Y-m-t')), 'incomes' => $this->getIncomes(date('Y-m-01'), date('Y-m-t')), 'expensesSum' => $this->getExpensesSum(date('Y-m-01'), date('Y-m-t')), 'expenses' => $this->getExpenses(date('Y-m-01'), date('Y-m-t')), 'balance' => $this->calculateBalance(date('Y-m-01'), date('Y-m-t')), 'incomesCategories' => $this->getIncomesCategories(), 'expensesCategories' => $this->getExpensesCategories(), 'payment' => $this->getPaymentMethodsAssignedToUser()]);

    } elseif($period == 'previous-month') {

      View::renderTemplate('Balance/show.html', ['period' => $period, 'incomesSum' => $this->getIncomesSum(date('Y-m-01', strtotime(date('Y-m')." -1 month")), date('Y-m-t', strtotime(date('Y-m')." -1 month"))), 'incomes' => $this->getIncomes(date('Y-m-01', strtotime(date('Y-m')." -1 month")), date('Y-m-t', strtotime(date('Y-m')." -1 month"))), 'expensesSum' => $this->getExpensesSum(date('Y-m-01', strtotime(date('Y-m')." -1 month")), date('Y-m-t', strtotime(date('Y-m')." -1 month"))), 'expenses' => $this->getExpenses(date('Y-m-01', strtotime(date('Y-m')." -1 month")), date('Y-m-t', strtotime(date('Y-m')." -1 month"))), 'balance' => $this->calculateBalance(date('Y-m-01', strtotime(date('Y-m')." -1 month")), date('Y-m-t', strtotime(date('Y-m')." -1 month"))), 'incomesCategories' => $this->getIncomesCategories(), 'expensesCategories' => $this->getExpensesCategories(), 'payment' => $this->getPaymentMethodsAssignedToUser()]);

    } elseif($period == 'current-year') {

      View::renderTemplate('Balance/show.html', ['period' => $period, 'incomesSum' => $this->getIncomesSum(date('Y-01-01'), date('Y-12-31')), 'incomes' => $this->getIncomes(date('Y-01-01'), date('Y-12-31')), 'expensesSum' => $this->getExpensesSum(date('Y-01-01'), date('Y-12-31')), 'expenses' => $this->getExpenses(date('Y-01-01'), date('Y-12-31')), 'balance' => $this->calculateBalance(date('Y-01-01'), date('Y-12-31')), 'incomesCategories' => $this->getIncomesCategories(), 'expensesCategories' => $this->getExpensesCategories(), 'payment' => $this->getPaymentMethodsAssignedToUser()]);

    } else {

      View::renderTemplate('Balance/show.html', ['period' => $period, 'incomesSum' => $this->getIncomesSum($_POST['from'], $_POST['to']), 'incomes' => $this->getIncomes($_POST['from'], $_POST['to']), 'expensesSum' => $this->getExpensesSum($_POST['from'], $_POST['to']), 'expenses' => $this->getExpenses($_POST['from'], $_POST['to']), 'balance' => $this->calculateBalance($_POST['from'], $_POST['to']), 'from' => $_POST['from'], 'to' => $_POST['to'], 'incomesCategories' => $this->getIncomesCategories(), 'expensesCategories' => $this->getExpensesCategories(), 'payment' => $this->getPaymentMethodsAssignedToUser()]);

    }
  }

  /**
   * Get incomes sum grouped by categories assigned to user
   * @param date $startDate Start date
   * @param date $endDate End date
   * 
   * @return mixed Incomes object if found, false otherwise
   */
  public function getIncomesSum($startDate, $endDate) {
    $sql = 'SELECT SUM(incomes.amount) Sum, incomes_category_assigned_to_users.name income_name 
            FROM incomes
            INNER JOIN incomes_category_assigned_to_users
            ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id
            WHERE incomes.user_id = :id
            AND incomes.date_of_income BETWEEN :startDate AND :endDate
            GROUP BY incomes_category_assigned_to_users.name
            ORDER BY Sum DESC';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
    $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get incomes assigned to user
   * @param date $startDate Start date
   * @param date $endDate End date
   * 
   * @return mixed Incomes object if found, false otherwise
   */
  public function getIncomes($startDate, $endDate) {
    $sql = 'SELECT incomes.id, incomes.date_of_income, incomes.amount, incomes.income_comment, incomes_category_assigned_to_users.name income_name 
            FROM incomes
            INNER JOIN incomes_category_assigned_to_users
            ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id
            WHERE incomes.user_id = :id
            AND incomes.date_of_income BETWEEN :startDate AND :endDate';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
    $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get expenses sum grouped by categories assigned to user
   * @param date $startDate Start date
   * @param date $endDate End date
   * 
   * @return mixed Expenses object if found, false otherwise
   */
  public function getExpensesSum($startDate, $endDate) {
    $sql = 'SELECT SUM(expenses.amount) Sum, expenses_category_assigned_to_users.name expense_name 
            FROM expenses
            INNER JOIN expenses_category_assigned_to_users
            ON expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id
            WHERE expenses.user_id = :id
            AND expenses.date_of_expense BETWEEN :startDate AND :endDate
            GROUP BY expenses_category_assigned_to_users.name
            ORDER BY Sum DESC';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
    $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get expenses assigned to user
   * @param date $startDate Start date
   * @param date $endDate End date
   * 
   * @return mixed Expenses object if found, false otherwise
   */
  public function getExpenses($startDate, $endDate) {
    $sql = 'SELECT expenses.id, expenses.date_of_expense, expenses.amount, expenses.expense_comment, expenses_category_assigned_to_users.name expense_name, payment_methods_assigned_to_users.name
            FROM expenses
            INNER JOIN expenses_category_assigned_to_users
            ON expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id
            INNER JOIN payment_methods_assigned_to_users
            ON payment_methods_assigned_to_users.id = expenses.payment_method_assigned_to_user_id
            WHERE expenses.user_id = :id
            AND expenses.date_of_expense BETWEEN :startDate AND :endDate';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
    $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get incomes categories assigned to user
   * 
   * @return mixed Incomes object if found, false otherwise
   */
  public function getIncomesCategories() {
    $sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :id';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get expenses categories assigned to user
   * 
   * @return mixed Expenses object if found, false otherwise
   */
  public function getExpensesCategories() {
    $sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :id';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get payment methods assigned to user
   * 
   * @return mixed Payment methods object if found, false otherwise
   */
  public function getPaymentMethodsAssignedToUser() {
    $sql = 'SELECT * FROM payment_methods_assigned_to_users
            WHERE user_id = :id';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
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

      if($this->saveIncome()) {

        Flash::addMessage('Income updated');

      } else {

        Flash::addMessage('Income was not updated, please try again', Flash::WARNING);

     }

    }
    
    $this->showBalance();
    
  }

  /**
   * Save edited income to incomes table
   * 
   * @return boolean True if the income was updated, false otherwise
   */
  public function saveIncome() {
    $sql = 'UPDATE incomes
            SET income_category_assigned_to_user_id = :income_category,
            amount = :amount,
            date_of_income = :date_of_income,
            income_comment = :income_comment
            WHERE id = :income_id;';

    $db = User::getDB();
            
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':income_category', $_POST['category'], PDO::PARAM_INT);
    $stmt->bindValue(':amount', $_POST['number'], PDO::PARAM_STR);
    $stmt->bindValue(':date_of_income', $_POST['date'], PDO::PARAM_STR);
    if ($_POST['comment']) {
      $stmt->bindValue(':income_comment', $_POST['comment'], PDO::PARAM_STR);
    } else {
      $stmt->bindValue(':income_comment', '', PDO::PARAM_STR);
    }
    $stmt->bindValue(':income_id', $_POST['income_id'], PDO::PARAM_INT);

    return $stmt->execute();
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

      if($this->saveExpense()) {

        Flash::addMessage('Expense updated');

      } else {

        Flash::addMessage('Expense was not updated, please try again', Flash::WARNING);

      }

    }
    
    $this->showBalance();
  }

  /**
   * Save edited expense to expenses table
   * 
   * @return boolean True if the expense was updated, false otherwise
   */
  public function saveExpense() {
    $sql = 'UPDATE expenses
            SET expense_category_assigned_to_user_id = :expense_category,
            payment_method_assigned_to_user_id = :payment,
            amount = :amount,
            date_of_expense = :date_of_expense,
            expense_comment = :expense_comment
            WHERE id = :expense_id;';

    $db = User::getDB();
            
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':expense_category', $_POST['category'], PDO::PARAM_INT);
    $stmt->bindValue(':payment', $_POST['payment'], PDO::PARAM_INT);
    $stmt->bindValue(':amount', $_POST['number'], PDO::PARAM_STR);
    $stmt->bindValue(':date_of_expense', $_POST['date'], PDO::PARAM_STR);
    if ($_POST['comment']) {
      $stmt->bindValue(':expense_comment', $_POST['comment'], PDO::PARAM_STR);
    } else {
      $stmt->bindValue(':expense_comment', '', PDO::PARAM_STR);
    }
    $stmt->bindValue(':expense_id', $_POST['expense_id'], PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Delete income
   * 
   * @return void
   */
  public function deleteIncomeAction() {
    $sql = 'DELETE FROM incomes 
            WHERE id = :income_id;';

    $db = User::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':income_id', $_POST['income_id'], PDO::PARAM_INT);

    $stmt->execute();

    Flash::addMessage('Income deleted');

    $this->showBalance();
  }

  /**
   * Delete expense
   * 
   * @return void
   */
  public function deleteExpenseAction() {
    $sql = 'DELETE FROM expenses 
            WHERE id = :expense_id;';

    $db = User::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':expense_id', $_POST['expense_id'], PDO::PARAM_INT);

    $stmt->execute();

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
    $incomesSumArr = $this->getIncomesSum($startDate, $endDate);
    $expensesSumArr = $this->getExpensesSum($startDate, $endDate);

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