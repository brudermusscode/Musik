<?php

use Bruder\Model\Artist;
use Bruder\Model\Genre;
use Bruder\Model\Mood;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var ?Artist
 */
$Artist = Artist::findOrReturn($id, "Kein Künstler")?->load("genres");

ob_start(); ?>

<popup-close>
  <mi>sentiment_satisfied</mi>
</popup-close>

<popup-container>
  <popup-container__content p42 posrel style=z-index:2; elevated>
    <form request="artist:update" reload update-current-track update-library responder=simple fl fldircol gap=mid enctype="multipart/form-data">
      <div fl fldircol gap=smol+>
        <div mt32 mb32>
          <div select-album-cover fl jucc>
            <picture widest rounded ovhid
              <?= $Artist->art ? "filled" : "" ?>>
              <img <?= $Artist->art
                      ? 'src="' . $Artist->art_link() . '"'
                      : "src" ?> />
              <mi cover color=quadro>face_3</mi>
              <mbutton trigger-file-input blur=smol material background=hover-dark size=mid icon-only>
                <input type=file accept="image/*" name=art hidden />
                <mi>edit</mi>
              </mbutton>
            </picture>
          </div>
        </div>

        <input autofocus crazy type=text name=name placeholder="Wie heißt er/sie/es?"
          value="<?= $Artist->name ?>" />
      </div>

      <div fl fldircol gap=smol+>
        <p text bold std ttup>Genres</p>
        <div fl flex-wrap gap=smol alic>
          <?php

          $Genres = Genre::all();

          foreach ($Genres as $Genre) :
            $genre_active = $Artist->genres->contains("id", $Genre->id);

          ?>
            <mbutton background=slighterer-light checkbox flone material smol no-word-wrap <?= $genre_active ? "active" : "" ?>>
              <?= $Genre->name ?>
              <input type=hidden name=genres[<?= $Genre->id ?>]
                value="<?= $genre_active ? 1 : 0 ?>" />
            </mbutton>
          <?php endforeach; ?>
        </div>
      </div>

      <div fl fldircol gap=smol+>
        <p text bold std ttup>Stimmungen</p>
        <div fl flex-wrap gap=smol alic>
          <?php

          foreach (Mood::all() as $Mood) :
            $mood_active = $Artist->moods->contains("id", $Mood->id);

          ?>
            <mbutton background=slighterer-light checkbox flone material smol no-word-wrap <?= $mood_active ? "active" : "" ?>>
              <?= $Mood->name ?>
              <input type=hidden name=moods[<?= $Mood->id ?>]
                value="<?= $mood_active ? 1 : 0 ?>" />
            </mbutton>
          <?php endforeach; ?>
        </div>
      </div>

      <input type=hidden name=id value="<?= $Artist->id ?>" />

      <div fl jucend alic>
        <mbutton material size=mid icon-only color=tertiary hoverable outlined submit-closest
          has-tooltip=left no-trans-delay>
          <mi>pregnant_woman</mi>
          <div ttooltip>Pööörfekt!</div>
        </mbutton>
      </div>
    </form>
  </popup-container__content>
</popup-container>


<?php

die($Request->success(data: ob_get_clean()));
