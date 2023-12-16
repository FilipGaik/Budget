<?php

namespace App\Controllers;

use PDO;
use \Core\View;
use \App\Models\SettingsModel;
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
    View::renderTemplate('Settings/show.html', [
      'user' => $this->user, 
      'incomesCategories' => SettingsModel::getIncomesCategories($_SESSION['user_id']), 
      'expensesCategories' => SettingsModel::getExpensesCategories($_SESSION['user_id']), 
      'paymentMethods' => SettingsModel::getPaymentMethods($_SESSION['user_id'])
    ]);
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

      $incomesCategories = SettingsModel::getIncomesCategories($_SESSION['user_id']);
      $incomeCategoryExists = false;

      foreach($incomesCategories as $incomeCategory) {

        if(strtoupper($incomeCategory[2]) == strtoupper($_POST['incomeCategory'])) {

          Flash::addMessage('Sorry, income category already exists, please enter different category name.', Flash::WARNING);

          $incomeCategoryExists = true;
        }
      }
        
      if(!$incomeCategoryExists) {

        if (SettingsModel::addIncomeCategoryToTable($_SESSION['user_id'], $_POST['incomeCategory'])) {

          Flash::addMessage('Income category added');
    
        } else {
    
          Flash::addMessage('Income category was not added, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
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

      $expensesCategories = SettingsModel::getExpensesCategories($_SESSION['user_id']);
      $expenseCategoryExists = false;

      foreach($expensesCategories as $expenseCategory) {

        if(strtoupper($expenseCategory[2]) == strtoupper($_POST['expenseCategory'])) {

          Flash::addMessage('Sorry, expense category already exists, please enter different category name.', Flash::WARNING);

          $expenseCategoryExists = true;
        }
      }
        
      if(!$expenseCategoryExists) {

        if (SettingsModel::addExpenseCategoryToTable($_SESSION['user_id'], $_POST['expenseCategory'])) {

          Flash::addMessage('Expense category added');
    
        } else {
    
          Flash::addMessage('Expense category was not added, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
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

      $paymentMethods = SettingsModel::getPaymentMethods($_SESSION['user_id']);
      $paymentMethodExists = false;

      foreach($paymentMethods as $paymentMethod) {

        if(strtoupper($paymentMethod[2]) == strtoupper($_POST['payment'])) {

          Flash::addMessage('Sorry, payment method already exists, please enter different method name.', Flash::WARNING);

          $paymentMethodExists = true;
        }
      }
        
      if(!$paymentMethodExists) {

        if (SettingsModel::addPaymentToTable($_SESSION['user_id'], $_POST['payment'])) {

          Flash::addMessage('Payment method added');
    
        } else {
    
          Flash::addMessage('Payment method was not added, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
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

      $incomesCategories = SettingsModel::getIncomesCategories($_SESSION['user_id']);
      $incomeCategoryExists = false;

      foreach($incomesCategories as $incomeCategory) {

        if(strtoupper($incomeCategory[2]) == strtoupper($_POST['incomeCategory'])) {

          Flash::addMessage('Sorry, income category already exists, please enter different category name.', Flash::WARNING);

          $incomeCategoryExists = true;
        }
      }
        
      if(!$incomeCategoryExists) {

        if (SettingsModel::renameIncomeCategoryInTable($_POST['incomeCategory'], $_POST['incomeCategoryId'])) {

          Flash::addMessage('Income category renamed');
    
        } else {
    
          Flash::addMessage('Income category was not renamed, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
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

      $expensesCategories = SettingsModel::getExpensesCategories($_SESSION['user_id']);
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

        $limitAmount = isset($_POST['limitOn']) ? $_POST['limitAmount'] : '0';

        if (SettingsModel::updateExpenseCategoryInTable($_POST['expenseCategory'], $limitAmount, $_POST['expenseCategoryId'])) {

          Flash::addMessage('Expense category updated');
    
        } else {
    
          Flash::addMessage('Expense category was not updated, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
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

      $paymentMethods = SettingsModel::getPaymentMethods($_SESSION['user_id']);
      $paymentMethodExists = false;

      foreach($paymentMethods as $paymentMethod) {

        if(strtoupper($paymentMethod[2]) == strtoupper($_POST['payment'])) {

          Flash::addMessage('Sorry, payment method already exists, please enter different method name.', Flash::WARNING);

          $paymentMethodExists = true;
        }
      }
        
      if(!$paymentMethodExists) {

        if (SettingsModel::renamePaymentInTable($_POST['payment'], $_POST['paymentId'])) {

          Flash::addMessage('Payment method renamed');
    
        } else {
    
          Flash::addMessage('Payment method was not renamed, please try again', Flash::WARNING);
        }
      }
    }

    $this->redirect('/settings/show');
  }

  /**
   * Delete income category
   * 
   * @return void
   */
  public function deleteIncomeCategoryAction() {
    SettingsModel::deleteIncomeCategory($_POST['incomeCategoryId']);

    Flash::addMessage('Income category deleted');

    $this->redirect('/settings/show');
  }

  /**
   * Delete expense category
   * 
   * @return void
   */
  public function deleteExpenseCategoryAction() {
    SettingsModel::deleteExpenseCategory($_POST['expenseCategoryId']);

    Flash::addMessage('Expense category deleted');

    $this->redirect('/settings/show');
  }

  /**
   * Delete payment method
   * 
   * @return void
   */
  public function deletePaymentMethodAction() {
    SettingsModel::deletePaymentMethod($_POST['paymentMethodId']);

    Flash::addMessage('Payment method deleted');

    $this->redirect('/settings/show');
  }

  /**
   * Delete account
   * 
   * @return void
   */
  public function deleteAccountAction() {
    SettingsModel::deleteAccount($_SESSION['user_id']);
  
    Auth::logout();
    
    $this->redirect('/');
  }

  /**
   * Get expenses categories and payment methods assigned to user from expenses table
   * 
   * @return void
   */
  public function getExpenses() {
    echo json_encode(SettingsModel::getExpenses($_SESSION['user_id']), JSON_UNESCAPED_UNICODE);
  }

  /**
   * Get incomes categories assigned to user from incomes table
   * 
   * @return void
   */
  public function getIncomes() {
    echo json_encode(SettingsModel::getIncomes($_SESSION['user_id']), JSON_UNESCAPED_UNICODE);
  }
}
