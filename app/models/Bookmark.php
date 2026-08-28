<?php

namespace Bruder\Model;

use Bruder\Bruder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Bookmark extends Bruder
{

  /**
   * @var string
   */
  protected $table = "bookmarks";

  /**
   * @var array
   */
  protected $fillable = [
    "ref_id",
    "type",
    "view_index",
    "deleted_at",
    "updated_at",
  ];

  public function new(object $params)
  {

    // # Type validation happens in Controller.

    /**
     * @var Album|Playlist|Artist|Track
     */
    $Object = $params->Object;

    /**
     * @var ?Bookmark
     */
    $LatestBookmark = self::latest()->first();

    /**
     * Create it!
     */
    $Bookmark = $Object->bookmark()
      ->create([
        "type" => strtolower(class_basename($Object)),
        "reference_id" => $Object->id,
        "view_index" => ($LatestBookmark->view_index ?? 0) + 1,
      ]);

    return $this->success(data: $Bookmark);
  }

  public function remove() {}

  /**
   * @return BelongsTo<Album>
   */
  public function album()
  {
    return $this->belongsTo(Album::class, "reference_id", "id");
  }

  /**
   * @return BelongsTo<Playlist>
   */
  public function playlist()
  {
    return $this->belongsTo(Playlist::class, "reference_id", "id");
  }

  /**
   * @return BelongsTo<Artist>
   */
  public function artist()
  {
    return $this->belongsTo(Artist::class, "reference_id", "id");
  }

  /**
   * @return BelongsTo<Track>
   */
  public function track()
  {
    return $this->belongsTo(Track::class, "reference_id", "id");
  }

  /**
   * Through polymorphic relations this model can have different
   * reference models based on the type sepcified.
   *
   * @return ?Album|Playlist|Artist|Track
   */
  public function reference()
  {
    return match ($this->type) {
      "album" => $this->album,
      "playlist" => $this->playlist,
      "artist" => $this->artist,
      "track" => $this->track,
      default => null,
    };
  }

  /**
   * @return string
   */
  public function url()
  {
    return "/" . $this->type . "/" . $this->reference_id;
  }

  /**
   * @return ?array|string
   */
  public function art_link()
  {
    $path = match ($this->type) {
      "artist" => "/data/user/1/portraits/",
      default => "/data/user/1/art/",
    };

    $Reference = $this->reference();
    if (!$Reference) return null;

    /**
     * @var array
     */
    $final = [];

    /**
     * Playlist, which has no custom artwork uploaded.
     */
    if ($this->type === "playlist" && !$Reference->art) {

      $artworks = $Reference->artworks();

      if ($artworks->count())
        foreach ($artworks as $art)
          $final[] = $path . $art;
    }

    /**
     * Anything else.
     */
    else $final = ($Reference->art ? $path . $Reference->art : null);

    return $final;
  }
}
