<?php

namespace App\Models;

use PDO;
use Core\View;

/**
 * Expenses model
 * 
 * PHP version 7.0
 */
class Expenses extends \Core\Model {
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
   * @return mixed Payment object if found, false otherwise
   */
  public static function getPaymentMethods($user_id) {
    $sql = 'SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :id';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Add expense to expense table
   * @param integer $user_id User ID
   * @param integer $expense_category_assigned_to_user_id Expense category
   * @param integer $payment_method_assigned_to_user_id Payment method
   * @param float $amount Amount
   * @param date $date_of_expense Date
   * @param string $expense_comment Comment
   * 
   * @return boolean True if the expense was added, false otherwise
   */
  public static function addExpense($user_id, $expense_category_assigned_to_user_id, $payment_method_assigned_to_user_id, $amount, $date_of_expense, $expense_comment) {
    $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
            VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment);';

    $db = static::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':expense_category_assigned_to_user_id', $expense_category_assigned_to_user_id, PDO::PARAM_INT);
    $stmt->bindValue(':payment_method_assigned_to_user_id', $payment_method_assigned_to_user_id, PDO::PARAM_INT);
    $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
    $stmt->bindValue(':date_of_expense', $date_of_expense, PDO::PARAM_STR);
    if ($expense_comment) {

      $stmt->bindValue(':expense_comment', $expense_comment, PDO::PARAM_STR);

    } else {

      $stmt->bindValue(':expense_comment', '', PDO::PARAM_STR);
    }

    return $stmt->execute();
  }

  /**
   * Get limit from expenses_category_assigned_to_users table for user and category
   * @param integer $user_id User ID
   * @param string $category Category name
   * 
   * @return array Limit
   */
  public static function getLimit($user_id, $category) {
    $sql = 'SELECT `limit` FROM expenses_category_assigned_to_users WHERE user_id = :id AND name = :category';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Get expense id, category name, amount and date
   * @param integer $user_id User ID
   * 
   * @return array
   */
  public static function getExpensesCatAmountAndDate($user_id) {
    $sql = 'SELECT id, expense_category_assigned_to_user_id, amount, date_of_expense
            FROM expenses
            WHERE user_id = :id;';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }
}
