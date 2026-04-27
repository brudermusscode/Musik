<?php

namespace Bruder\Application;

use Bruder\Http\Domain;

class Session
{

  /**
   * @var array
   */
  public static $localhost_domains = [
    "localhost",
    "127.0.0.1",
  ];

  /**
   * @var string
   */
  private static $session_name = "__sess__";

  /**
   * @param ?string $cacheExpire
   * @param ?string $cacheLimiter
   * @return void
   */
  public function __construct(
    ?string $cacheExpire = null,
    ?string $cacheLimiter = null
  ) {
    self::begin($cacheExpire, $cacheLimiter);
  }

  /**
   * @return boolean
   */
  public static function key_exists($name)
  {
    return isset($_SESSION[$name]);
  }

  /**
   * @param string $name
   * @return ?string
   */
  public static function get(string|int $name)
  {
    return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
  }

  /**
   * @param string $name
   * @param string $value
   * @return true
   */
  public static function set(string $name, mixed $value)
  {

    /**
     * Understanding references with (&)
     * $a = 5;
     * $b = &$a;  // $b now refers to the same data as $a
     * $b = 10;
     * echo $a;  // Outputs 10 because $a and $b reference the same data
     */

    /**
     * function increment(&$num) {
     *    $num++;
     * }
     *
     * $count = 5;
     * increment($count);
     *
     * echo $count; // is now 6
     */

    $_SESSION[$name] = $value;

    return true;
  }

  /**
   * @return true
   */
  public static function remove(string $name)
  {
    if (isset($_SESSION[$name]))
      unset($_SESSION[$name]);

    return true;
  }

  /**
   * Fully destroys a session and attempts to begin an empty one.
   * It also removes all persistent cookies for user authentication.
   *
   * @return void
   */
  public static function cancel()
  {
    /**
     * Destroy the session if it is active.
     */
    if (!self::is_inactive()) {
      session_unset();
      session_destroy();
    }

    /**
     * Begin a new one.
     */
    return self::begin();
  }

  /**
   * Begins a new session.
   *
   * @param string $cacheExpire
   * @param string $cacheLimiter
   * @return void
   */
  public static function begin(
    ?string $cacheExpire = null,
    ?string $cacheLimiter = null
  ) {
    /**
     * Return, if there is an active session.
     */
    if (!self::is_inactive())
      return;

    /**
     * Set cache limiter?
     */
    if ($cacheLimiter !== null) {
      session_cache_limiter($cacheLimiter);
    }

    /**
     * Set cache expiry?
     */
    if ($cacheExpire !== null) {
      session_cache_expire($cacheExpire);
    }

    /**
     * PHPSESSID cookie parameter.
     */
    session_set_cookie_params([
      'lifetime' => 438000 * 60,
      'path' => '/',
      'domain' => Domain::clean($_SERVER['HTTP_HOST']),

      // TODO: Implement better validation for localhost
      'secure' => current_env() === "dev" ? false : true,

      /**
       * httponly flag is for restricting access to the cookie.
       * Setting it to true will prevent JavaScript from being
       * able to access it. Good for session cookies.
       */
      'httponly' => true,

      /**
       * Lax will send cookies when coming or going to another,
       * external site. Strict will prevent this and offers the
       * highest security standart.
       */
      'samesite' => "Lax",
    ]);

    /**
     * Set a custom name for the PHPSESSID cookie.
     */
    session_name(self::$session_name);

    /**
     * Start the session.
     */
    session_start();
  }

  /**
   * Checks if a PHP Session is not existing.
   *
   * @return bool
   */
  public static function is_inactive()
  {
    return session_status() === PHP_SESSION_NONE;
  }
}
