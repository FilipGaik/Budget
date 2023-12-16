<?php

namespace App\Models;

use PDO;
use Core\View;

/**
 * Balance model
 * 
 * PHP version 7.0
 */
class BalanceModel extends \Core\Model {
  /**
   * Get incomes sum grouped by categories assigned to user
   * @param integer $user_id User ID
   * @param date $startDate Start date
   * @param date $endDate End date
   * 
   * @return mixed Incomes object if found, false otherwise
   */
  public static function getIncomesSum($user_id, $startDate, $endDate) {
    $sql = 'SELECT SUM(incomes.amount) Sum, incomes_category_assigned_to_users.name income_name 
            FROM incomes
            INNER JOIN incomes_category_assigned_to_users
            ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id
            WHERE incomes.user_id = :id
            AND incomes.date_of_income BETWEEN :startDate AND :endDate
            GROUP BY incomes_category_assigned_to_users.name
            ORDER BY Sum DESC';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
    $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get incomes assigned to user
   * @param integer $user_id User ID
   * @param date $startDate Start date
   * @param date $endDate End date
   * 
   * @return mixed Incomes object if found, false otherwise
   */
  public static function getIncomes($user_id, $startDate, $endDate) {
    $sql = 'SELECT incomes.id, incomes.date_of_income, incomes.amount, incomes.income_comment, incomes_category_assigned_to_users.name income_name 
            FROM incomes
            INNER JOIN incomes_category_assigned_to_users
            ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id
            WHERE incomes.user_id = :id
            AND incomes.date_of_income BETWEEN :startDate AND :endDate';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
    $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get expenses sum grouped by categories assigned to user
   * @param integer $user_id User ID
   * @param date $startDate Start date
   * @param date $endDate End date
   * 
   * @return mixed Expenses object if found, false otherwise
   */
  public static function getExpensesSum($user_id, $startDate, $endDate) {
    $sql = 'SELECT SUM(expenses.amount) Sum, expenses_category_assigned_to_users.name expense_name 
            FROM expenses
            INNER JOIN expenses_category_assigned_to_users
            ON expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id
            WHERE expenses.user_id = :id
            AND expenses.date_of_expense BETWEEN :startDate AND :endDate
            GROUP BY expenses_category_assigned_to_users.name
            ORDER BY Sum DESC';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
    $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get expenses assigned to user
   * @param integer $user_id User ID
   * @param date $startDate Start date
   * @param date $endDate End date
   * 
   * @return mixed Expenses object if found, false otherwise
   */
  public static function getExpenses($user_id, $startDate, $endDate) {
    $sql = 'SELECT expenses.id, expenses.date_of_expense, expenses.amount, expenses.expense_comment, expenses_category_assigned_to_users.name expense_name, payment_methods_assigned_to_users.name
            FROM expenses
            INNER JOIN expenses_category_assigned_to_users
            ON expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id
            INNER JOIN payment_methods_assigned_to_users
            ON payment_methods_assigned_to_users.id = expenses.payment_method_assigned_to_user_id
            WHERE expenses.user_id = :id
            AND expenses.date_of_expense BETWEEN :startDate AND :endDate';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
    $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get incomes categories assigned to user
   * @param integer $user_id User ID
   * 
   * @return mixed Incomes object if found, false otherwise
   */
  public static function getIncomesCategories($user_id) {
    $sql = 'SELECT * FROM incomes_category_assigned_to_users 
            WHERE user_id = :id';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get expenses categories assigned to user
   * @param integer $user_id User ID
   * 
   * @return mixed Expenses object if found, false otherwise
   */
  public static function getExpensesCategories($user_id) {
    $sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :id';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get payment methods assigned to user
   * @param integer $user_id User ID
   * 
   * @return mixed Payment methods object if found, false otherwise
   */
  public static function getPaymentMethodsAssignedToUser($user_id) {
    $sql = 'SELECT * FROM payment_methods_assigned_to_users
            WHERE user_id = :id';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Save edited income to incomes table
   * @param integer $income_category_assigned_to_user_id Income category
   * @param float $amount Amount
   * @param date $date_of_income Date
   * @param integer $income_id Income ID
   * @param string $income_comment Comment
   * 
   * @return boolean True if the income was updated, false otherwise
   */
  public static function saveIncome($income_category_assigned_to_user_id, $amount, $date_of_income, $income_id, $income_comment) {
    $sql = 'UPDATE incomes
            SET income_category_assigned_to_user_id = :income_category_assigned_to_user_id,
            amount = :amount,
            date_of_income = :date_of_income,
            income_comment = :income_comment
            WHERE id = :income_id;';

    $db = static::getDB();
            
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':income_category_assigned_to_user_id', $income_category_assigned_to_user_id, PDO::PARAM_INT);
    $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
    $stmt->bindValue(':date_of_income', $date_of_income, PDO::PARAM_STR);
    if ($income_comment) {

      $stmt->bindValue(':income_comment', $income_comment, PDO::PARAM_STR);

    } else {

      $stmt->bindValue(':income_comment', '', PDO::PARAM_STR);
    }
    $stmt->bindValue(':income_id', $income_id, PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Save edited expense to expenses table
   * @param integer $expense_category_assigned_to_user_id Expense category
   * @param integer $payment_method_assigned_to_user_id Payment method
   * @param float $amount Amount
   * @param date $date_of_expense Date
   * @param integer $expense_id Expense ID
   * @param string $expense_comment Comment
   * 
   * @return boolean True if the expense was updated, false otherwise
   */
  public static function saveExpense($expense_category_assigned_to_user_id, $payment_method_assigned_to_user_id, $amount, $date_of_expense, $expense_id, $expense_comment) {
    $sql = 'UPDATE expenses
            SET expense_category_assigned_to_user_id = :expense_category_assigned_to_user_id,
            payment_method_assigned_to_user_id = :payment_method_assigned_to_user_id,
            amount = :amount,
            date_of_expense = :date_of_expense,
            expense_comment = :expense_comment
            WHERE id = :expense_id;';

    $db = static::getDB();
            
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':expense_category_assigned_to_user_id', $expense_category_assigned_to_user_id, PDO::PARAM_INT);
    $stmt->bindValue(':payment_method_assigned_to_user_id', $payment_method_assigned_to_user_id, PDO::PARAM_INT);
    $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
    $stmt->bindValue(':date_of_expense', $date_of_expense, PDO::PARAM_STR);
    if ($expense_comment) {

      $stmt->bindValue(':expense_comment', $expense_comment, PDO::PARAM_STR);

    } else {

      $stmt->bindValue(':expense_comment', '', PDO::PARAM_STR);
    }
    $stmt->bindValue(':expense_id', $expense_id, PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Delete income
   * @param integer $income_id Income ID
   * 
   * @return void
   */
  public static function deleteIncome($income_id) {
    $sql = 'DELETE FROM incomes 
            WHERE id = :income_id;';

    $db = static::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':income_id', $income_id, PDO::PARAM_INT);

    $stmt->execute();
  }

  /**
   * Delete expense
   * @param integer $expense_id Expense ID
   * 
   * @return void
   */
  public static function deleteExpense($expense_id) {
    $sql = 'DELETE FROM expenses 
            WHERE id = :expense_id;';

    $db = static::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':expense_id', $expense_id, PDO::PARAM_INT);

    $stmt->execute();
  }
}