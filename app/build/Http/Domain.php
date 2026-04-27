<?php

namespace Bruder\Http;

class Domain
{
  /**
   * All possible localhost domains & IP addresses.
   */
  public static $localhost_domains = [
    "localhost",
    "127.0.0.1",
  ];

  /**
   * Serializes the domain name for localhost and domains with ports.
   *
   * @param string $domain The domain name to serialize
   * @return string The domain name
   */
  public static function clean($domain)
  {
    /**
     * Return the local development domain if it is the dev environment.
     */
    if (current_env() === "dev")
      return _env("DOMAIN");

    /**
     * Remove port
     */
    $name = preg_replace("/:[0-9]+/", "", $domain);

    /**
     * Remove www.
     */
    $name = str_replace('www.', '', $name);

    /**
     * Remove the leading dot if there.
     */
    if ($name[0] == '.')
      $name = substr($name, 1);


    /**
     * Set to localhost
     */
    if (in_array($name, self::$localhost_domains))
      return "localhost";

    return $name;
  }
}
