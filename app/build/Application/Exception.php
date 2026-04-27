<?php

namespace Bruder\Application;

class Exception
{
  /**
   * Log an exception into the database.
   *
   * @param \Exception $exception The exception to be logged.
   * @throws \Exception If an error occurs while logging the exception.
   * @return boolean Returns true or false based on outcome
   */
  public static function log($exception, string $section = null)
  {
    // TODO: Rebuild auto-logging exceptions.
  }
}
