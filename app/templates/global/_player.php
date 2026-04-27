<?php

use Bruder\Application\Cookie;

?>

<show-player data-action="player:hide" <?= Cookie::get("__player_collapsed") == 1 ? "collapsed" : "" ?>>
  <mi mid>music_cast</mi>
</show-player>

<!--- Volume will be populated by js. --->
<player volume="<?= CURRENT_VOLUME ?>" <?= Cookie::get("__player_collapsed") == 1 ? "collapsed" : "" ?>>
  <player-content fl alic jucsb>

    <player-overflow>
      <duration-track></duration-track>
    </player-overflow>

    <player-metadata>
      <picture fl alic jucc>
        <img />
        <mi wide>album</mi>
      </picture>
      <div fl fldircol alistart>
        <p title text smolplus bold trimt>
          <span style="display:block;width:164px;height:12px;margin-bottom:8px;margin-top:10px;background:rgba(0,0,0,.12);" rounded></span>
        </p>
        <p artist text smol regular lh1 slight fl alic gap=smoler background=slight-light rounded=smol pl6 pr8 pblock4 clickable="no-shadow">
          <mi>artist</mi>
          <span style="display:block;width:60px;height:6px;background:rgba(0,0,0,.12);" rounded></span>
        </p>
      </div>
    </player-metadata>

    <player-actions fl alic gap=smol>
      <div fl alic gap=smol+>
        <mbutton play-previous previous material icon-only size=mid hoverable no-hover-shadow>
          <mi>arrow_back_2</mi>
        </mbutton>
        <mbutton play material icon-only size=mid>
          <mi></mi>
        </mbutton>
        <mbutton play-next next material icon-only size=mid hoverable no-hover-shadow>
          <mi>play_arrow</mi>
        </mbutton>
      </div>

      <div style="height:60px;width:2px;background:rgba(0,0,0,.12);" rounded minline12></div>

      <volume-controls fl alic gap=smol>
        <div controls elevated>
          <mbutton volume-up material size=mid icon-only hoverable>
            <mi>add</mi>
          </mbutton>
          <mbutton volume-down material size=mid icon-only hoverable>
            <mi>remove</mi>
          </mbutton>
        </div>

        <div volume-display volume-mute>
          <div muted></div>
          <mi style=font-size:1.4em;></mi>
        </div>
      </volume-controls>

      <mbutton data-action="player:hide" next material icon-only size=mid hoverable no-hover-shadow>
        <mi>keyboard_arrow_down </mi>
      </mbutton>
    </player-actions>
  </player-content>
</player>