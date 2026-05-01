<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Queue;

class QueueController extends Controller
{


  /**
   * UPDATE
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["type", "id", "track_id"],
      optional: ["shuffle"],
    );

    return (new Queue)->new($this->params);
  }
}
