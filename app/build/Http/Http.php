<?php

namespace Bruder\Http;

class Http
{
  /**
   * Checks for an authorization in the request being existent.
   *
   * @param string $type
   * @param array $server PHP $_SERVER super global
   * @return ?string The request token
   */
  public static function authorization(string $type, ?array $server = null)
  {
    /**
     * @var array
     */
    $server = $server ?? $_SERVER;

    /**
     * @var string
     */
    $auth_token = isset($server['HTTP_AUTHORIZATION']) ? $server['HTTP_AUTHORIZATION'] : '';

    /**
     * Type is valid?
     */
    if (strpos($auth_token, $type) !== false) {
      $tokenParts = explode(' ', $auth_token);
      $auth_token = isset($tokenParts[1]) ? $tokenParts[1] : '';

      return $auth_token ? $auth_token : null;
    }

    return false;
  }
}
