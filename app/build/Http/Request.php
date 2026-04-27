<?php

namespace Bruder\Http;

use Bruder\Application\Application;

class Request
{

  /**
   * @var object
   */
  protected static $return;

  public $message;
  public $error;
  public $status;
  public $data;
  private $dd;

  public function __construct()
  {
    $this->dd = Application::get_default_dialogues();
    $this->message = "Bruder, was ist passiert?";
    $this->status = !true;
    $this->data = null;
    $this->error = $this->message;
  }

  /**
   * Sets the given message or a fallback default as the error and
   * returns itself.
   *
   * @param string $message
   * @return object|string
   */
  public function error(?string $message = null, mixed $data = null, bool $json_encoded = true)
  {
    /**
     * Bang messages refer to default dialogues.
     * @var bool
     */
    $is_default_dialogue = $message && $message[0] === "!";

    /**
     * @var string
     */
    $message = $is_default_dialogue
      ? (
        $this->dd[str_replace("!", "", $message)] ?? $this->dd["INVALID_REQUEST"]
      )
      : (
        $message ?? $this->dd["INVALID_REQUEST"]
      );

    $this->data = $data;
    $this->message = $message;
    $this->error = $message;

    return $json_encoded ? json_encode($this) : $this;
  }

  /**
   * Sets the message to the given one, removes the error and sets
   * the status to true. Returns itself.
   *
   * @param ?string $message
   * @param ?mixed $data
   * @param string $message
   * @return object|string
   */
  public function success(?string $message = null, mixed $data = null, bool $json_encoded = true)
  {
    /**
     * Bang messages refer to default dialogues.
     * @var bool
     */
    $is_default_dialogue = $message && $message[0] === "!";

    /**
     * @var string
     */
    $message = $is_default_dialogue
      ? (
        $this->dd[str_replace("!", "", $message)] ?? "Geil"
      )
      : (
        $message ?? "Geil"
      );

    $this->message = $message;
    $this->data = $data;
    $this->error = null;
    $this->status = true;

    return $json_encoded ? json_encode($this) : $this;
  }

  /**
   * Checks if request is of type POST
   *
   * @return bool
   */
  public static function is_post()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') return true;

    return false;
  }

  /**
   * Checks if request is of type GET
   *
   * @return bool
   */
  public static function is_get()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') return true;

    return false;
  }

  /**
   * Retrieves and escapes a query parameter from the URL.
   *
   * This function checks if the specified query parameter exists
   * in the URL's query string.
   * If the parameter is found, its value is retrieved and
   * properly escaped using htmlspecialchars()
   * to prevent potential cross-site scripting (XSS) attacks.
   *
   * @param string $name The name of the query parameter to
   *    retrieve and escape.
   * @param string|null $default The default value to return if
   *    the parameter is not found. Default: null.
   * @return string|null The escaped value of the query parameter,
   *    or null if the parameter is not found.
   */
  public static function sanitize_get_param(string $name, mixed $default = null)
  {
    if (isset($_GET[$name]))
      return htmlspecialchars($_GET[$name]);

    return $default;
  }

  /**
   * Return real estate client ip
   * @return string
   */
  public static function get_remote_address()
  {
    if (!isset($_SERVER['REMOTE_ADDR'])) return NULL;

    $proxy_header = "HTTP_X_FORWARDED_FOR";
    $trusted_proxies = ["2001:db8::1", "192.168.50.1"];

    if (in_array($_SERVER['REMOTE_ADDR'], $trusted_proxies)) {

      if (array_key_exists($proxy_header, $_SERVER)) {

        $proxy_list = explode(",", $_SERVER[$proxy_header]);
        $client_ip = trim(end($proxy_list));

        if (filter_var($client_ip, FILTER_VALIDATE_IP)) {
          return $client_ip;
        } else {
          // Validation failed - beat the guy who configured the proxy or
          // the guy who created the trusted proxy list?
          // TODO: some error handling to notify about the need of punishment
        }
      }
    }

    return $_SERVER['REMOTE_ADDR'];
  }

  /**
   * Validates whether a given string contains only integers
   * separated by commas.

   *
   * Examples:
   * - "1,2,3,4,5" - valid
   * - "123" - valid
   * - "1, 2,3" - invalid
   * - "1,,3" - invalid
   * - "1,2,a,4" - invalid
   *
   * @param string $input The string to validate.
   * @return bool True if the string is valid, false otherwise.
   */
  public static function get_is_array_of_ints(string $input)
  {
    return preg_match('/^\d+(,\d+)*$/', $input) === 1;
  }
}
