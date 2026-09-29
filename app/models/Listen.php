<?php

namespace Bruder\Model;

use Bruder\Bruder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Listen extends Bruder
{

  /**
   * @var array
   */
  protected $fillable = [
    "track_id",
    "relation_id",
    "relation_type",
  ];

  /**
   * @return BelongsTo<Track>
   */
  public function track()
  {
    return $this->belongsTo(Track::class);
  }

  /**
   * @return BelongsTo<Album>
   */
  public function album()
  {
    return $this->belongsTo(Album::class, "relation_id");
  }

  /**
   * @return BelongsTo<Playlist>
   */
  public function playlist()
  {
    return $this->belongsTo(Playlist::class, "relation_id");
  }

  /**
   * @return Playlist|Album|null
   */
  public function relation()
  {
    return match ($this->relation_type) {
      "album" => $this->album,
      "playlist" => $this->playlist,
      default => null,
    };
  }
}
