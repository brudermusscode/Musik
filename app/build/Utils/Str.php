<?php

namespace Bruder\Utils;

class Str
{

  /**
   * Checks if all of the gioven $characters are existent in $string.
   *
   * @param string $string
   * @param string $characters
   * @return bool
   */
  public static function contains_any(string $string, string $characters)
  {
    foreach (str_split($characters) as $char)
      if (strpos($string, $char) === false)
        return false;

    return true;
  }

  /**
   * Escape multiple strings using htmlentities.
   *
   * @param string ...$strings The strings to be escaped.
   * @return array The array of escaped strings.
   */
  public static function escape(string ...$strings)
  {
    $escapedStrings = [];

    foreach ($strings as $string) {
      $escapedString = htmlentities($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      $escapedStrings[] = $escapedString;
    }

    return $escapedStrings;
  }

  /**
   * Strips all possible whitespaces from a given string and returns
   * the string without these
   *
   * @param string The string to be stripped
   * @return string
   */
  public static function strip_whitespace(string $str): string
  {
    return preg_replace('/\s+/', '', $str);
  }

  /**
   *  Validates the length of a string
   *
   * @param string $str The string to be validated
   * @param array $range The range from to
   * @return boolean Whether or not it's in range
   */
  public static function validate_length(string $str, array $range = [0, 32])
  {
    return (strlen($str) >= $range[0] && strlen($str) <= $range[1]);
  }

  /**
   * The theme name comes with dashes, remove those and replace them
   * with spaces and uppercase all first letters.
   *
   * @return string
   */
  public static function format_theme_name($str): string
  {
    $formatted_theme_name = explode("-", $str);

    foreach ($formatted_theme_name as $key => $a)
      $formatted_theme_name[$key] = ucfirst($a);

    return implode(" ", $formatted_theme_name);
  }

  /**
   * Encrypts a string with a given password.
   *
   * @param string $str
   * @param string $password
   * @return string The encoded string
   */
  public static function openssl_encrypt_with_password(string $str, string $password)
  {
    $key = openssl_digest($password, 'SHA256', true);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($str, 'aes-256-cbc', $key, 0, $iv);

    return base64_encode($iv . $encrypted);
  }

  /**
   * Decrypts a string with a given password.
   *
   * @param string $str
   * @param string $password
   * @return string The decoded string
   */
  public static function openssl_decrypt_with_password(string $str, string $password)
  {
    $key = openssl_digest($password, 'SHA256', true);
    $data = base64_decode($str);
    $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = substr($data, openssl_cipher_iv_length('aes-256-cbc'));

    return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
  }

  /**
   * @return string
   */
  public static function truncate(string $inputString, int $maxLength = 100)
  {
    if (strlen($inputString) > $maxLength) {
      $truncatedString = substr($inputString, 0, $maxLength) . "&hellip;";
      return $truncatedString;
    } else {
      return $inputString;
    }
  }

  /**
   * Checks if the given string is a valid year between 1900 and 2099.
   *
   * @param string $str
   * @return bool
   */
  public static function is_valid_year(string $str)
  {
    return preg_match('/^(19|20)\d{2}$/', $str) === 1;
  }
}
