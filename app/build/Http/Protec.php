<?php

namespace Bruder\Http;

class Protec
{

  /**
   * Validate Cloudflare Turnstile in production mode.
   *
   * @param string $token
   * @return bool
   */
  public static function spam(string $token)
  {

    /**
     * If the token is empty, return false of course.
     */
    if (!$token) return false;

    /**
     * @var string
     */
    $url = _env("CLOUDFLARE_TURNSTILE_SITE_VERIFY_URL");

    /**
     * @var string
     */
    $secret_key = _env("CLOUDFLARE_TURNSTILE_SECRET_KEY");

    /**
     * @var array
     */
    $data = [
      "secret" => $secret_key,
      "response" => $token,
      "remoteip" => $_SERVER["REMOTE_ADDR"]
    ];

    /**
     * @var array
     */
    $options = [
      "http" => [
        "header"  => "Content-type: application/x-www-form-urlencoded",
        "method"  => "POST",
        "content" => http_build_query($data)
      ]
    ];

    /**
     * @var resource
     */
    $context = stream_context_create($options);

    /**
     * @var string|false
     */
    $result = file_get_contents($url, false, $context);

    /**
     * @var ?array
     */
    $result_transformed = $result ? json_decode($result, true) : null;

    return is_array($result_transformed) && isset($result_transformed["success"]) ? $result_transformed["success"] : false;
  }
}