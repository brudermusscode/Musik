<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Album;

class AlbumsController extends Controller
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
      optional: ["art", "release_year"],
    );

    return (new Album)->new($this->params);
  }

  /**
   * UPDATE
   *
   * @return string
   */
  public function update()
  {

    /**
     * Validate params.
     */
    $this->validate_params(
      strict: ["id", "name"],
      optional: ["art", "release_year"],
    );

    /**
     * @var ?Album
     */
    $Album = Album::findOrReturn($this->params->id, "<strong>Bruder, Album gibts nicht!</strong>");

    return $Album->edit($this->params);
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

    /**
     * @var ?Album
     */
    $Album = Album::findOrReturn($this->params->id, "<strong>Ne man, das gibts nicht!</strong>");

    /**
     * Delete Album and Relations.
     */
    $Album->delete();
    $Album->bookmark()->delete();
    $Album->album_tracks()->delete();

    if ($Album->art)
      unlink(_root() . "/public/data/user/1/art/" . $Album->art);

    return $this->success("<strong>Album ist gone!</strong>");
  }
}
