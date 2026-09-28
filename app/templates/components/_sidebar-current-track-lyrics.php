<?php

use Bruder\Model\Track;
use Bruder\Model\Artist;
use Bruder\Model\Album;
use Bruder\Model\Playlist;
use Bruder\Utils\Str;

/**
 * @var Track $CurrentTrack
 * @var Album|Playlist|null $Relation
 * @var int $id
 * @var string $type
 * @var bool $has_video
 * @var bool $track_relates
 */

if ($CurrentTrack->lyrics_w_timestamps) : ?>

  <lyrics-placeholder></lyrics-placeholder>
  <lyrics animation=zoom-in>
    <div title fl alic jucsb>
      <p window-light pinline14 pblock8 text smol ttup semibold>Lyrics</p>
      <div fl alic gap=smol>
        <mbutton data-action="lyrics:fullscreen" material smol icon-only>
          <mi>resize</mi>
        </mbutton>
        <mbutton request-get="track:lyrics-edit" player-stop
          data-id="<?= $CurrentTrack->id ?>" material smol icon-only>
          <mi>edit</mi>
        </mbutton>
        <mbutton shadow-submit
          request="track:update"
          data-id="<?= $CurrentTrack->id ?>"
          data-lyrics_w_timestamps=""
          update-current-track
          material smol icon-only>
          <mi>delete</mi>
        </mbutton>
      </div>
    </div>


    <lyrics-content>
      <?php foreach ($CurrentTrack->lyrics_formatted() as $key => $line) :

        $seconds = Str::timestamp_to_seconds($line->timestamp);

      ?>
        <line timestamp="<?= $seconds; ?>">
          <form data-form="track:lyrics-update-inline">
            <line-actions>
              <mi midler slight>border_color</mi>
            </line-actions>

            <timestamp><?= $line->timestamp; ?></timestamp>
            <p><?= $line->content; ?></p>

            <input type=hidden name="lyrics_line_key" crazy value="<?= $key; ?>" />
            <input type=text name="lyrics_line_timestamp" enter-submitable crazy
              value="<?= $line->timestamp; ?>" />
            <input type=text name="lyrics_line_content" enter-submitable crazy
              value="<?= $line->content; ?>" />
            <input type=hidden name="id" value="<?= $CurrentTrack->id; ?>" />
            <mbutton material icon-only submit-closest>
              <mi>done</mi>
            </mbutton>
          </form>
        </line>
      <?php endforeach ?>
    </lyrics-content>
  </lyrics>

<?php else: ?>

  <div
    request-get="track:lyrics-add"
    data-id=<?= $CurrentTrack->id ?>
    window-light pr18 pl62 pblock18 hoverable ovhid posrel rounded=mid>
    <mi color=<?= Track::COLOR ?>
      style="position:absolute;bottom:-18px;left:-6px;font-size:52px;">
      lyrics</mi>
    <div fl alic flone gap=smol jucsb>
      <p text semibold trimt>Lyrics hinzufügen</p>
      <mi>add</mi>
    </div>
  </div>

<?php endif ?>