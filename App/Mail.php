<?php

namespace App;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/src/SMTP.php';

/**
 * Mail
 * 
 * PHP version 7.0
 */
class Mail {
  /**
   * Send a message
   * 
   * @param string $to Recipient
   * @param string $subject Subject
   * @param string $text Text-only content of the message
   * @param string $html HTML content of the message
   * 
   * @return mixed
   */
  public static function send($to, $subject, $text, $html) {
    
    $mail = new PHPMailer(true);

    //try {

      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = Config::GMAIL_USERNAME;
      $mail->Password = Config::GMAIL_PASSWORD;
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->SetFrom('no-reply@budget.com', 'no-reply'); // 'no-reply@budget.com' does not work
      $mail->addReplyTo('no-reply@budget.com', 'no-reply');
      $mail->addAddress($to);
      $mail->Subject = $subject;
      $mail->Body = $text; // Was = $html;

      $mail->send();

    /*
      echo 'Message sent';

    } catch (Exception $e) {

      echo 'Message not sent: ', $mail->ErrorInfo;
    }
    */
    
    /*
    $mg = new Mailgun(Config::MAILGUN_API_KEY);
    $domain = Config::MAILGUN_DOMAIN;

    # Now, compose and send your message
    $mg->sendMessage($domain, array('from' => 'filipgaik@gmail.com', 'to' => $to, 'subject' => $subject, 'text' => $text, 'html' => $html));
    */
  }
}