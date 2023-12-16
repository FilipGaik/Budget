<?php

namespace App\Models;

use PDO;
use Core\View;

/**
 * Settings model
 * 
 * PHP version 7.0
 */
class SettingsModel extends \Core\Model {
  /**
   * Get incomes categories assigned to user
   * @param integer $user_id User ID
   * 
   * @return mixed Incomes object if found, false otherwise
   */
  public static function getIncomesCategories($user_id) {
    $sql = 'SELECT * FROM incomes_category_assigned_to_users 
            WHERE user_id = :user_id';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    
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
    $sql = 'SELECT * FROM expenses_category_assigned_to_users 
            WHERE user_id = :user_id';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    
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
    $sql = 'SELECT * FROM payment_methods_assigned_to_users 
            WHERE user_id = :user_id';
    
    $db = static::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Add new income category to incomes_category_assigned_to_users table
   * @param integer $user_id User ID
   * @param string $name Name of income category
   * 
   * @return boolean True if the income category was added, false otherwise
   */
  public static function addIncomeCategoryToTable($user_id, $name) {
    $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
            VALUES (:user_id, :name);';

    $db = static::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);    

    return $stmt->execute();
  }

  /**
   * Add new expense category to expenses_category_assigned_to_users table
   * @param integer $user_id User ID
   * @param string $name Name of expense category
   * 
   * @return boolean True if the expense category was added, false otherwise
   */
  public static function addExpenseCategoryToTable($user_id, $name) {
    $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
            VALUES (:user_id, :name);';

    $db = static::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);    

    return $stmt->execute();
  }

  /**
   * Add new payment method to payment_methods_assigned_to_users table
   * @param integer $user_id User ID
   * @param string $name Name of payment method
   * 
   * @return boolean True if the payment method was added, false otherwise
   */
  public static function addPaymentToTable($user_id, $name) {
    $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
            VALUES (:user_id, :name);';

    $db = static::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);    

    return $stmt->execute();
  }

  /**
   * Rename income category in incomes_category_assigned_to_users table
   * @param string $name Name of income category
   * @param integer $id Income category ID
   * 
   * @return boolean True if the income category was renamed, false otherwise
   */
  public static function renameIncomeCategoryInTable($name, $id) {
    $sql = 'UPDATE incomes_category_assigned_to_users
            SET name = :name
            WHERE incomes_category_assigned_to_users.id = :id;';

    $db = static::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);    

    return $stmt->execute();
  }

  /**
   * Rename expense category or and turn on or off monthly limit for expense category in expenses_category_assigned_to_users table
   * @param string $name Name of expense category
   * @param float $limitAmount Limit amount
   * @param integer $id Expense category ID
   * 
   * @return boolean True if the expense category was updated, false otherwise
   */
  public static function updateExpenseCategoryInTable($name, $limitAmount, $id) {
    $sql = 'UPDATE expenses_category_assigned_to_users
            SET name = :name, `limit` = :limitAmount
            WHERE expenses_category_assigned_to_users.id = :id;';

    $db = static::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':limitAmount', $limitAmount, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Rename payment method in payment_methods_assigned_to_users table
   * @param string $name Name of payment method
   * @param integer $id Payment method ID
   * 
   * @return boolean True if the payment method was renamed, false otherwise
   */
  public static function renamePaymentInTable($name, $id) {
    $sql = 'UPDATE payment_methods_assigned_to_users
            SET name = :name
            WHERE payment_methods_assigned_to_users.id = :id;';

    $db = static::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);    

    return $stmt->execute();
  }

  /**
   * Delete income category
   * @param integer $id Income category ID
   * 
   * @return void
   */
  public static function deleteIncomeCategory($id) {
    $sql = 'DELETE FROM incomes
            WHERE income_category_assigned_to_user_id = :id;
            DELETE FROM incomes_category_assigned_to_users 
            WHERE id = :id;';

    $db = static::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();
  }

  /**
   * Delete expense category
   * @param integer $id Expense category ID
   * 
   * @return void
   */
  public static function deleteExpenseCategory($id) {
    $sql = 'DELETE FROM expenses
            WHERE expense_category_assigned_to_user_id = :id;
            DELETE FROM expenses_category_assigned_to_users 
            WHERE id = :id;';

    $db = static::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();
  }

  /**
   * Delete payment method
   * @param integer $id Payment method ID
   * 
   * @return void
   */
  public static function deletePaymentMethod($id) {
    $sql = 'DELETE FROM expenses
            WHERE payment_method_assigned_to_user_id = :id;
            DELETE FROM payment_methods_assigned_to_users 
            WHERE id = :id;';

    $db = static::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();
  }

  /**
   * Delete account
   * @param integer $id User ID
   * 
   * @return void
   */
  public static function deleteAccount($id) {
    $sql = 'DELETE FROM expenses
            WHERE user_id = :id;
            DELETE FROM expenses_category_assigned_to_users 
            WHERE user_id = :id;
            DELETE FROM incomes
            WHERE user_id = :id;
            DELETE FROM incomes_category_assigned_to_users 
            WHERE user_id = :id;
            DELETE FROM payment_methods_assigned_to_users 
            WHERE user_id = :id;
            DELETE FROM users
            WHERE id = :id;';

    $db = static::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();
  }

  /**
   * Get expenses categories and payment methods assigned to user from expenses table
   * @param integer $id User ID
   * 
   * @return void
   */
  public static function getExpenses($id) {
    $sql = 'SELECT expense_category_assigned_to_user_id, payment_method_assigned_to_user_id
            FROM expenses
            WHERE user_id = :id;';

    $db = static::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Get incomes categories assigned to user from incomes table
   * @param integer $id User ID
   * 
   * @return void
   */
  public static function getIncomes($id) {
    $sql = 'SELECT income_category_assigned_to_user_id
            FROM incomes
            WHERE user_id = :id;';

    $db = static::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
  }
}