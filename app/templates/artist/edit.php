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

<form request="artist:update" reload update-current-track update-library
  responder=simple enctype="multipart/form-data">
  <popup-container>
    <popup-container__content p42 posrel style=z-index:2; elevated fl fldircol gap=mid>
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

        <div slider dno>
          <slider>
            <nibble></nibble>
          </slider>
          <!--<input type=hidden name=weight value="0.00" />-->
        </div>
      </div>

      <input type=hidden name=id value="<?= $Artist->id ?>" />
    </popup-container__content>

    <mbutton flone tabindex="3" material size=wide has-icon=right window
      submit-closest color=tertiary>
      <mi>pregnant_woman</mi>
    </mbutton>
  </popup-container>
</form>


<?php

die($Request->success(data: ob_get_clean()));
