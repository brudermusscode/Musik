<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Artist;

class ArtistsController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["name"],
      optional: [],
    );

    return error("No time for creating Artists");
  }

  /**
   * UPDATE
   *
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: ["id",],
      optional: ["art"],
    );

    /**
     * @var ?Artist
     */
    $Artist = Artist::findOrReturn($this->params->id, "<strong>Bruder, Artist gibts nicht!</strong>");

    return $Artist->edit($this->params);
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

    return error("No time for deleting Artists");
  }
}
