<?php

namespace Bruder\Job;

use Bruder\Model\Album;

class SyncAlbums
{
  public function execute()
  {
    return (new Album)->job_sync_albums();
  }
}
