<?php

namespace Bruder\Trait;

trait HasTracks
{

  /**
   * @return ?object All artworks for albums of tracks in this
   * playlist uniquely grouped.
   */
  public function artworks()
  {

    /**
     * @var Collection<Track>
     */
    return $this->tracks()
      ->whereHas('album', fn($q) => $q->whereNotNull('art'))
      ->with('album')
      ->get()
      ->pluck('album.art')
      ->unique()
      ->values() ?? null;
  }
}
