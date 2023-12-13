<?php

namespace App\Controllers;

use PDO;
use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Settings controller
 * 
 * PHP version 7.0
 */
class Settings extends Authenticated {
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
   * Show settings page
   * 
   * @return void
   */
  public function showAction() {
    View::renderTemplate('Settings/show.html', ['user' => $this->user, 'incomesCategories' => $this->getIncomesCategories(), 'expensesCategories' => $this->getExpensesCategories(), 'paymentMethods' => $this->getPaymentMethods()]);
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
   * @return mixed Payment object if found, false otherwise
   */
  public function getPaymentMethods() {
    $sql = 'SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :id';
    
    $db = User::getDB();
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll();
  }

  /**
   * Add new income category
   * 
   * @return void
   */
  public function addIncomeCategoryAction() {
    if($_POST['incomeCategory'] == '') {

      Flash::addMessage('Please do not remove required attribute from input tag.', Flash::WARNING);

    } else {

      $incomesCategories = $this->getIncomesCategories();
      $incomeCategoryExists = false;

      foreach($incomesCategories as $incomeCategory) {

        if(strtoupper($incomeCategory[2]) == strtoupper($_POST['incomeCategory'])) {

          Flash::addMessage('Sorry, income category already exists, please enter different category name.', Flash::WARNING);

          $incomeCategoryExists = true;
        }
      }
        
      if(!$incomeCategoryExists) {

        if ($this->addIncomeCategoryToTable()) {

          Flash::addMessage('Income category added');
    
        } else {
    
          Flash::addMessage('Income category was not added, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
  }

  /**
   * Add new income category to incomes_category_assigned_to_users table
   * 
   * @return boolean True if the income category was added, false otherwise
   */
  private function addIncomeCategoryToTable() {
    $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
            VALUES (:user_id, :name);';

    $db = User::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':name', $_POST['incomeCategory'], PDO::PARAM_STR);    

    return $stmt->execute();
  }

  /**
   * Add new expense category
   * 
   * @return void
   */
  public function addExpenseCategoryAction() {
    if($_POST['expenseCategory'] == '') {

      Flash::addMessage('Please do not remove required attribute from input tag.', Flash::WARNING);

    } else {

      $expensesCategories = $this->getExpensesCategories();
      $expenseCategoryExists = false;

      foreach($expensesCategories as $expenseCategory) {

        if(strtoupper($expenseCategory[2]) == strtoupper($_POST['expenseCategory'])) {

          Flash::addMessage('Sorry, expense category already exists, please enter different category name.', Flash::WARNING);

          $expenseCategoryExists = true;
        }
      }
        
      if(!$expenseCategoryExists) {

        if ($this->addExpenseCategoryToTable()) {

          Flash::addMessage('Expense category added');
    
        } else {
    
          Flash::addMessage('Expense category was not added, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
  }

  /**
   * Add new expense category to expenses_category_assigned_to_users table
   * 
   * @return boolean True if the expense category was added, false otherwise
   */
  private function addExpenseCategoryToTable() {
    $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
            VALUES (:user_id, :name);';

    $db = User::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':name', $_POST['expenseCategory'], PDO::PARAM_STR);    

    return $stmt->execute();
  }

  /**
   * Add new payment method
   * 
   * @return void
   */
  public function addPaymentAction() {
    if($_POST['payment'] == '') {

      Flash::addMessage('Please do not remove required attribute from input tag.', Flash::WARNING);

    } else {

      $paymentMethods = $this->getPaymentMethods();
      $paymentMethodExists = false;

      foreach($paymentMethods as $paymentMethod) {

        if(strtoupper($paymentMethod[2]) == strtoupper($_POST['payment'])) {

          Flash::addMessage('Sorry, payment method already exists, please enter different method name.', Flash::WARNING);

          $paymentMethodExists = true;
        }
      }
        
      if(!$paymentMethodExists) {

        if ($this->addPaymentToTable()) {

          Flash::addMessage('Payment method added');
    
        } else {
    
          Flash::addMessage('Payment method was not added, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
  }

  /**
   * Add new payment method to payment_methods_assigned_to_users table
   * 
   * @return boolean True if the payment method was added, false otherwise
   */
  private function addPaymentToTable() {
    $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
            VALUES (:user_id, :name);';

    $db = User::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':name', $_POST['payment'], PDO::PARAM_STR);    

    return $stmt->execute();
  }

  /**
   * Rename income category
   * 
   * @return void
   */
  public function renameIncomeCategoryAction() {
    if($_POST['incomeCategory'] == '') {

      Flash::addMessage('Please do not remove required attribute from input tag.', Flash::WARNING);

    } else {

      $incomesCategories = $this->getIncomesCategories();
      $incomeCategoryExists = false;

      foreach($incomesCategories as $incomeCategory) {

        if(strtoupper($incomeCategory[2]) == strtoupper($_POST['incomeCategory'])) {

          Flash::addMessage('Sorry, income category already exists, please enter different category name.', Flash::WARNING);

          $incomeCategoryExists = true;
        }
      }
        
      if(!$incomeCategoryExists) {

        if ($this->renameIncomeCategoryInTable()) {

          Flash::addMessage('Income category renamed');
    
        } else {
    
          Flash::addMessage('Income category was not renamed, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
  }

  /**
   * Rename income category in incomes_category_assigned_to_users table
   * 
   * @return boolean True if the income category was renamed, false otherwise
   */
  private function renameIncomeCategoryInTable() {
    $sql = 'UPDATE incomes_category_assigned_to_users
            SET name = :name
            WHERE incomes_category_assigned_to_users.id = :id;';

    $db = User::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':name', $_POST['incomeCategory'], PDO::PARAM_STR);
    $stmt->bindValue(':id', $_POST['incomeCategoryId'], PDO::PARAM_INT);    

    return $stmt->execute();
  }

  /**
   * Rename expense category or and turn on or off monthly limit for expense category
   * 
   * @return void
   */
  public function renameExpenseCategoryAction() {
    if($_POST['expenseCategory'] == '') {

      Flash::addMessage('Please do not remove required attribute from input tag.', Flash::WARNING);

    } else {

      $expensesCategories = $this->getExpensesCategories();
      $expenseCategoryExists = false;

      foreach($expensesCategories as $expenseCategory) {

        if($expenseCategory['id'] == $_POST['expenseCategoryId']) {

          continue;

        } elseif(strtoupper($expenseCategory[2]) == strtoupper($_POST['expenseCategory'])) {

          Flash::addMessage('Sorry, expense category already exists, please enter different category name.', Flash::WARNING);

          $expenseCategoryExists = true;
        }
      }
        
      if(!$expenseCategoryExists) {

        if ($this->updateExpenseCategoryInTable()) {

          Flash::addMessage('Expense category updated');
    
        } else {
    
          Flash::addMessage('Expense category was not updated, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
  }

  /**
   * Rename expense category or and turn on or off monthly limit for expense category in expenses_category_assigned_to_users table
   * 
   * @return boolean True if the expense category was updated, false otherwise
   */
  private function updateExpenseCategoryInTable() {
    $sql = 'UPDATE expenses_category_assigned_to_users
            SET name = :name, `limit` = :limitAmount
            WHERE expenses_category_assigned_to_users.id = :id;';

    $db = User::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':name', $_POST['expenseCategory'], PDO::PARAM_STR);
    if(isset($_POST['limitOn'])) {
      $stmt->bindValue(':limitAmount', $_POST['limitAmount'], PDO::PARAM_STR);
    } else {
      $stmt->bindValue(':limitAmount', 0, PDO::PARAM_INT);
    }
    $stmt->bindValue(':id', $_POST['expenseCategoryId'], PDO::PARAM_INT);

    return $stmt->execute();
  }

  /**
   * Rename payment method
   * 
   * @return void
   */
  public function renamePaymentAction() {
    if($_POST['payment'] == '') {

      Flash::addMessage('Please do not remove required attribute from input tag.', Flash::WARNING);

    } else {

      $paymentMethods = $this->getPaymentMethods();
      $paymentMethodExists = false;

      foreach($paymentMethods as $paymentMethod) {

        if(strtoupper($paymentMethod[2]) == strtoupper($_POST['payment'])) {

          Flash::addMessage('Sorry, payment method already exists, please enter different method name.', Flash::WARNING);

          $paymentMethodExists = true;
        }
      }
        
      if(!$paymentMethodExists) {

        if ($this->renamePaymentInTable()) {

          Flash::addMessage('Payment method renamed');
    
        } else {
    
          Flash::addMessage('Payment method was not renamed, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
  }

  /**
   * Rename payment method in payment_methods_assigned_to_users table
   * 
   * @return boolean True if the payment method was renamed, false otherwise
   */
  private function renamePaymentInTable() {
    $sql = 'UPDATE payment_methods_assigned_to_users
            SET name = :name
            WHERE payment_methods_assigned_to_users.id = :id;';

    $db = User::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':name', $_POST['payment'], PDO::PARAM_STR);
    $stmt->bindValue(':id', $_POST['paymentId'], PDO::PARAM_INT);    

    return $stmt->execute();
  }

  /**
   * Delete income category
   * 
   * @return void
   */
  public function deleteIncomeCategoryAction() {
    $sql = 'DELETE FROM incomes
            WHERE income_category_assigned_to_user_id = :id;
            DELETE FROM incomes_category_assigned_to_users 
            WHERE id = :id;
            ';

    $db = User::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_POST['incomeCategoryId'], PDO::PARAM_INT);

    $stmt->execute();

    Flash::addMessage('Income category deleted');

    $this->redirect('/settings/show');
  }

  /**
   * Delete expense category
   * 
   * @return void
   */
  public function deleteExpenseCategoryAction() {
    $sql = 'DELETE FROM expenses
            WHERE expense_category_assigned_to_user_id = :id;
            DELETE FROM expenses_category_assigned_to_users 
            WHERE id = :id;';

    $db = User::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_POST['expenseCategoryId'], PDO::PARAM_INT);

    $stmt->execute();

    Flash::addMessage('Expense category deleted');

    $this->redirect('/settings/show');
  }

  /**
   * Delete payment method
   * 
   * @return void
   */
  public function deletePaymentMethodAction() {
    $sql = 'DELETE FROM expenses
            WHERE payment_method_assigned_to_user_id = :id;
            DELETE FROM payment_methods_assigned_to_users 
            WHERE id = :id;';

    $db = User::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_POST['paymentMethodId'], PDO::PARAM_INT);

    $stmt->execute();

    Flash::addMessage('Payment method deleted');

    $this->redirect('/settings/show');
  }

  /**
   * Delete account
   * 
   * @return void
   */
  public function deleteAccountAction() {
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

    $db = User::getDB();
                
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);

    $stmt->execute();
    
    Auth::logout();
    
    $this->redirect('/');
  }

  /**
   * Get expenses categories and payment methods assigned to user from expenses table
   * 
   * @return void
   */
  public function getExpenses() {
    $sql = 'SELECT expense_category_assigned_to_user_id, payment_method_assigned_to_user_id
            FROM expenses
            WHERE user_id = :id;';

    $db = User::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);

    $stmt->execute();

    $arr = $stmt->fetchAll();

    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  }

  /**
   * Get incomes categories assigned to user from incomes table
   * 
   * @return void
   */
  public function getIncomes() {
    $sql = 'SELECT income_category_assigned_to_user_id
            FROM incomes
            WHERE user_id = :id;';

    $db = User::getDB();
        
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);

    $stmt->execute();

    $arr = $stmt->fetchAll();

    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  }
}