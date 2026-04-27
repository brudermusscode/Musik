<?php

namespace Bruder\Http;

class CSRF
{
  /**
   * @return string
   */
  public static function token()
  {
    $env_path = include _root() . '/config/security/tokens.php';
    return $env_path["csrf"];
  }

  /**
   * @return boolean
   */
  public static function validate_token(string $token)
  {
    $parts = explode('{{[|]}}', self::url_save_decode($token));
    if (count($parts) === 3) {
      $data = session_id() . $parts[1] . $parts[2];
      $hash = hash_hmac('sha256', $data, self::token(), true);

      if (hash_equals($hash, $parts[0])) return true;
    }

    return false;
  }

  /**
   * @return string
   */
  public static function create_token()
  {
    $seed = random_bytes(8);
    $time = time();
    $data = session_id() . $seed . $time;

    $hash = hash_hmac('sha256', $data, self::token(), true);

    return self::url_save_encode($hash . '{{[|]}}' . $seed . '{{[|]}}' . $time);
  }

  /**
   * @return string
   */
  public static function url_save_encode(string $m)
  {
    return rtrim(strtr(base64_encode($m), '+/', '-_'), '=');
  }

  /**
   * @return string
   */
  public static function url_save_decode(string $m)
  {
    return base64_decode(strtr($m, '-_', '+/'));
  }
}
