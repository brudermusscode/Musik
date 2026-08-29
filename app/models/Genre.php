<?php

namespace Bruder\Model;

use Bruder\Bruder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Bruder
{

  /**
   * @var array
   */
  protected $fillable = [
    "name",
  ];

  /**
   * @return BelongsToMany<Artist>
   */
  public function artists()
  {
    return $this->belongsToMany(Artist::class, "artist_genres");
  }
}
