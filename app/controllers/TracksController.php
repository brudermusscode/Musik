<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Track;

class TracksController extends Controller
{


  /**
   * UPDATE
   *
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: ["id"],
      optional: ["title", "artist", "file_name", "video", "genre", "year", "listens"],
    );

    /**
     * @var ?Track
     */
    $Object = Track::findOrReturn($this->params->id, "No Track");

    return $Object->edit($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id"],
      optional: [],
    );

    return $this->success("<strong>Album ist gone!</strong>");
  }
}
