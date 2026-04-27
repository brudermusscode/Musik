<?php

namespace Bruder\Application;

class Server
{
  /**
   * @param string $key
   * @return mixed
   */
  public static function get(string $key)
  {
    $key = strtoupper($key);

    return (isset($_SERVER[$key]) ? $_SERVER[$key] : null);
  }

  /**
   * @return array
   */
  public static function get_all()
  {
    return (isset($_SERVER) ? $_SERVER : null);
  }
}
