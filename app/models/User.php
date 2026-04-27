<?php

namespace Bruder\Model;

use Bruder\Bruder;

class User extends Bruder
{

  protected $fillable = [
    "display_name",
    "deleted_at",
    "updated_at",
  ];

  public function tracks()
  {
    return $this->hasMany(Track::class);
  }
}
