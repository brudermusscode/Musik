<?php

namespace Bruder\Model;

use Illuminate\Support\Collection;
use Bruder\Bruder;

class Queue extends Bruder
{

  protected $table = "";

  public const array TYPES = ["album", "playlist", "artist"];

  /**
   * @param object $params
   * @return ?Collection<Track>
   */
  public function new(object $params)
  {

    /**
     * @var ?Album|Playlist|Artist|Collection<Track>
     */
    $Object = self::fetch($params->type, $params->id);

    if (($Object instanceof Collection) && !($Object->first() instanceof Track))
      return error("First Object ist kein Track");

    /**
     * @var ?Track
     */
    $Track = Track::find($params->track_id);

    /**
     * ! Track not part of Object
     */
    if (
      !$Track ||
      ($Object instanceof Collection) && !$Object->contains($Track) ||
      !($Object instanceof Collection) && !$Object->tracks->contains($Track)
    )
      return error("no_track");

    /**
     * @var Collection<Track>
     */
    $Tracks = ($Object instanceof Collection) ? $Object : $Object->tracks;


    /**
     * @var array
     */
    $final = [];

    /**
     * Build an array with the queue ids in correct order based on
     * the type of the Relation.
     *
     * @var array
     */
    $final["Queue"] = (function () use ($Tracks) {
      $arr = [];

      foreach ($Tracks as $Track) {
        $arr[] = $Track->id;
      };

      return $arr;
    })();

    /**
     * Shuffle please?
     */
    if (isset($params->shuffle))
      shuffle($final["Queue"]);

    /**
     * Append the current's song index in that array which
     * represents the Queue.
     */
    $final["index"] = array_search($Track->id, $final["Queue"]);

    return success(data: $final);
  }

  /**
   * @param string $type
   * @param int $id
   * @return ?Collection<Track>
   */
  public static function fetch($type, $id)
  {
    return match ($type) {
      "album" => Album::with(["tracks" => function ($q) {
        $q->orderBy("album_tracks.id", "ASC");
      }])->find($id),
      "playlist" => Playlist::with(["tracks" => function ($q) {
        $q->orderBy("playlist_index", "ASC");
      }])->find($id),
      "artist" => Artist::with(["tracks" => function ($q) {
        $q->orderBy("listens", "DESC");
      }])->find($id),
      default => Track::orderBy("id", "DESC")->get(),
    };
  }
}
