<?php

namespace Bruder\Trait;

use Bruder\Http\Request;

trait ProcessesRequests
{

  /** ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, */
  /** ,,,,,,,,,,,,,,,,,,,,,, REQUEST & RETURN ,,,,,,,,,,,,,,,,,,,,,, */
  /** ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, */

  /**
   * @param string $message
   * @return object|string
   */
  public function error(?string $message = null, mixed $data = null, bool $json_encoded = true)
  {
    return (new Request)->error($message, $data, json_encoded: $json_encoded);
  }

  /**
   * @param ?string $message
   * @param ?mixed $data
   * @return object|string
   */
  public function success(?string $message = null, mixed $data = null, bool $json_encoded = true)
  {
    return (new Request)->success($message, $data, json_encoded: $json_encoded);
  }
}
