<?php

namespace Bruder\Exception;

use Exception;
use Bruder\Application;

class RouteConstraintException extends Exception
{
  public function __construct($message = "Route Constraint Unmatching", $code = 0, ?Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
    Application\Logger::to_file($this, "routing_errors.log");
  }
}
