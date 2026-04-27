<?php

namespace Bruder\Mail;

use Bruder\Application\Logger;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{

  /**
   * @param string $address
   * @param string $subject
   * @param mixed $body - Either HTML or text.
   * @param ?string $from_mail - Set where the mail should be
   *                coming from.
   * @param ?string $from_name - Set the name the mail should be
   *                coming from.
   * @param bool $debug
   * @return boolean
   */
  public function create(
    string $address,
    string $subject,
    mixed $body,
    ?string $from_mail = null,
    ?string $from_name = null,
    bool $debug = false
  ) {

    $private_key = _root() . "/config/keys/DKIM.key";
    $mail = new PHPMailer(true);
    $config = _env();

    /**
     * We need the DKIM key to send mails to Google & Co., or
     * otherwise they will bounce back and be declined by their servers.
     */
    if (!file_exists($private_key) && current_env() !== "dev")
      return false;

    /**
     * Important to set for PHPMailer.
     */
    date_default_timezone_set('Europe/Berlin');

    try {

      /**
       * Mail-Server configuration
       */
      $mail->isSMTP();
      $mail->Host = $config->MAIL_HOST;
      $mail->Port = $config->MAIL_PORT;
      $mail->Priority = 1;
      $mail->SMTPAuth = $config->MAIL_ENABLE_AUTH;
      $mail->Username = $config->MAIL_USERNAME;
      $mail->Password = $config->MAIL_PASSWORD;

      /**
       * Content & receipient
       */
      $mail->setFrom(
        $from_mail ?? $config->MAIL_FROM_MAIL,
        $from_name ?? $config->MAIL_FROM_NAME
      );
      $mail->Subject = $subject;
      $mail->isHTML(true);
      $mail->Body = $body;
      // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

      $mail->addAddress($address);

      /**
       * Set encoding & charset for proper displaying.
       */
      $mail->CharSet = "UTF-8";
      $mail->Encoding = "base64";


      /**
       * Debug settings.
       */
      if ($debug) {
        $mail->Debugoutput = "echo";
        $mail->SMTPDebug = 4; // Enable full debug output
      }

      /**
       * Production|Staging settings.
       */
      if (current_env() !== "dev") {
        /**
         * Encoding
         */
        $mail->AddCustomHeader("X-MSMail-Priority: High");
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        /**
         * DKIM
         */
        $mail->DKIM_domain = $config->DOMAIN;
        $mail->DKIM_selector = $config->MAIL_DKIM_SELECTOR;
        $mail->DKIM_private = $private_key;
        // $mail->DKIM_passphrase = $config->MAIL_DKIM_PASSPHRASE;
        $mail->DKIM_identity = $mail->From;
      }

      /**
       * Send it!
       */
      $mail->send();

      return true;
    } catch (\Exception $e) {

      /**
       * Log error.
       */
      Logger::to_file($e, "mail_errors.log");

      return false;
    }
  }
}
