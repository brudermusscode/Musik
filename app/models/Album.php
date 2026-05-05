<?php

namespace Bruder\Model;

use Illuminate\Support\Collection;
use Bruder\Bruder;
use Bruder\Utils\Str;
use Bruder\Utils\Utils;
use GuzzleHttp\Client;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Intervention\Image\Interfaces\ImageInterface;

class Album extends Bruder
{

  const string ICON = "album";
  const string COLOR = "quadro";

  /**
   * @var array
   */
  protected $fillable = [
    "artist_id",
    "name",
    "art",
    "release_year",
    "deleted_at",
    "updated_at",
  ];

  /**
   * Creates a new instance.
   *
   * @param object $params
   * @return string
   */
  public function new(object $params)
  {

    /**
     * @var self
     */
    $Object = self::make();

    /**
     * ? Name
     */
    if (isset($params->name)) {
      if (strlen(trim($params->name)) < 1)
        return $this->error("<strong>????? warum ?????</strong>");

      $Object->name = $params->name;
    }

    /**
     * ? Release Year
     */
    if (!empty($params->release_year)) {
      if (!Str::is_valid_year($params->release_year))
        return $this->error("<strong>Gib 1 richtiges Jahr an xD</strong>");

      $Object->release_year = $params->release_year;
    }

    /**
     * ? Art
     */
    if (!empty($params->art["tmp_name"])) {
      $upload = $Object->upload_art($params->art);

      if (!$upload->status)
        return $this->error("<strong>Fehler! Fehler!</strong> Bild!");

      $Object->art = $upload->data->filename;
    }

    try {
      $Object->save();
      $Object->db_commit();

      return $this->success(data: ["redirect_uri" => "/album/$Object->id"]);
    } catch (\Exception $e) {
      $Object->db_rollback();

      return $this->error($e->getMessage());
    }
  }

  /**
   * Updates an instance of this class.
   *
   * @param object $params
   * @return string
   */
  public function edit(object $params)
  {

    /**
     * ? Name
     */
    if (isset($params->name)) {
      if (strlen(trim($params->name)) < 1)
        return $this->error();

      $this->name = $params->name;
    }

    /**
     * ? Cover Art
     */
    if (!empty($params->art["tmp_name"])) {

      /**
       * @var object
       */
      $upload = $this->upload_art($params->art);

      if (!$upload->status)
        return $this->error("<strong>Fehler! Fehler!</strong> Bild!");

      $this->art = $upload->data->filename;
    }

    /**
     * ? Release Year
     */
    if (isset($params->release_year)) {
      if (strlen(trim($params->release_year)) > 0)
        if (!Str::is_valid_year($params->release_year))
          return $this->error("<strong>Gib 1 richtiges Jahr an xD</strong>");

      $this->release_year = $params->release_year ? (int) $params->release_year : null;
    }

    $this->db_transaction();

    try {

      $this->save();
      $this->db_commit();

      return $this->success("<strong>Aligth man! Gespeichert!</strong>");
    } catch (\Exception $e) {
      $this->db_rollback();
      return $this->error($e->getMessage());
    }
  }

  /**
   * Uploads a new file with a random alpha string as name and
   * encodes it as webp.
   *
   * @param array $file
   * @return object *->data holds the filename
   */
  public function upload_art(array $file)
  {

    /**
     * Die on any error.
     */
    \Bruder\File\Upload::error($file);

    $save_path = _root() . "/public/data/user/1/art";
    $file_name = Utils::random_alpha_token(24) . ".webp";

    try {

      /**
       * @var ImageManagerInterface
       */
      $ImageManager = ImageManager::usingDriver(GdDriver::class);

      /**
       * @var ImageInterface
       */
      $image = $ImageManager->decodePath($file["tmp_name"]);
      $encoded = $image->encodeUsingFormat(Format::WEBP, quality: 100);

      /**
       * Change the file name until it is really unique.
       */
      while (Album::where("art", $file_name)->first())
        $file_name = Utils::random_alpha_token(24) . ".webp";

      /**
       * @var string
       */
      $full_path = "$save_path/$file_name";


      // # Save!
      $encoded->save($full_path);

      return success(data: (object) ["filename" => $file_name], json_encoded: false);
    } catch (\Exception $e) {

      /**
       * Delete the file if it already exists.
       */
      if (isset($full_path) && file_exists($full_path))
        unlink($full_path);

      return error($e->getMessage());
    }
  }

  /**
   * @return ?Collection<Artist>
   */
  public function artists()
  {
    return $this->tracks
      ->map->artistt
      ->filter()
      ->unique("id")
      ->values();
  }

  /**
   * @return Collection<AlbumTrack>
   */
  public function album_tracks()
  {
    return $this->hasMany(AlbumTrack::class);
  }

  /**
   * Interessant wie leicht diese Pivot-Releation doch ist.
   *
   * @return Collection<Track>
   */
  public function tracks()
  {
    return $this->belongsToMany(Track::class, "album_tracks");
  }

  /**
   * @return ?Bookmark
   */
  public function bookmark()
  {
    return $this->hasOne(Bookmark::class, "reference_id", "id");
  }

  /**
   * Uses cURL to fetch song information from musicbrainz.
   *
   * @param string $artist
   * @param string $title
   * @return mixed
   */
  public static function curl_song(string $artist, string $title)
  {
    $userAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0';
    $q = sprintf('recording:"%s" AND artist:"%s"', $title, $artist);
    $encoded = urlencode($q);
    $url = "https://musicbrainz.org/ws/2/recording/?query={$encoded}&fmt=json&limit=5";

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: $userAgent", "Accept: application/json"]);

    $result = curl_exec($ch);

    return $result;
  }

  /**
   * Fetches possible album cover art from coverartarchive by
   * the corresponding mbid.
   *
   * @param string $mbid
   * @return string Most likely a redirect link to archive.org
   */
  function curl_album_art_redirect_url(string $mbid)
  {
    $userAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0';
    $url = "https://coverartarchive.org/release/{$mbid}";

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "User-Agent: $userAgent",
      "Accept: application/json"
    ]);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 307)
      return null;

    return str_replace("See: ", "", trim($result));
  }

  /**
   * Needs the redirect URL coming from curl_song_album.
   *
   * @param string $url
   * @return ?array
   */
  public static function curl_album_art(string $url)
  {
    $userAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0';
    $headers = [
      "User-Agent: $userAgent",
      "Accept: application/json"
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (!in_array($httpCode, [200, 302]))
      return null;

    if ($httpCode === 302) {
      $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

      $ch = curl_init($redirectUrl);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      $result = curl_exec($ch);
    }

    /**
     * @var ?array
     */
    $return = json_decode($result, true);

    return is_array($return) ? $return : null;
  }

  /**
   * This is more or less a job that fetches new albums added to
   * Music Brainz as well as possible cover images downloading
   * them  the public data directory of user 1.
   *
   * @return string
   */
  public function job_sync_albums()
  {

    /**
     * qvar array
     */
    $wrong = [];

    /**
     * @var Collection<Track>
     */
    $Tracks = Track::orderBy("id", "ASC")
      ->get();

    foreach ($Tracks as $key => $Track) :

      // if ($key >= 50)
      //   continue;

      $artist = $Track->artist;
      $title = $Track->title;
      $error_string = "$artist :: $title ::";

      /**
       * @var Artist
       */
      $Artist = Artist::where("name", $Track->artist)
        ->first();

      /**
       * Artist exists?
       */
      if (!$Artist) {
        $wrong[] = "$error_string Artist doesn't exist";
        continue;
      }

      /**
       * AlbumTrack exists?
       */
      if (AlbumTrack::where("track_id", $Track->id)->first()) {
        $wrong[] = "$error_string AlbumTrack exists";
        continue;
      }

      /**
       * Fetch the album information from music brainz.
       */
      $response = self::curl_song($artist, $title);

      // pdie(json_decode($response));


      if (!$response) {
        $wrong = "$error_string Error Code while fetching album";
        continue;
      }

      /**
       * @var array
       */
      $response = json_decode($response, true);

      /**
       * No album?
       */
      if (!isset($response["recordings"][0]["releases"][0]["title"])) {
        $wrong[] = "$error_string No album found";
        continue;
      }

      /**
       * @var array
       */
      $album = $response["recordings"][0]["releases"][0];

      /**
       * Begin transaction for safe database interaction.
       * @var Bruder
       * This just inserts 10 rows, don't know why.
       */
      // $Bruder = (new Bruder);
      // $Bruder->db_transaction();

      try {

        $album_has_existed = false;

        /**
         * @var ?Album
         */
        $Album = Album::where([
          "artist_id" => $Artist->id,
          "name" => $album["title"]
        ])->first();

        /**
         * Create a new album, if none has existed before.
         */
        if (!$Album) {

          /**
           * @var Album
           */
          $Album = Album::create([
            "artist_id" => $Artist->id,
            "name" => $album["title"],
          ]);
        } else
          $album_has_existed = true;

        /**
         * @var AlbumTrack
         */
        $AlbumTrack = $Album->album_tracks()
          ->create([
            "track_id" => $Track->id,
          ]);

        /**
         * Keep going, if the album has existed before. In this case
         * we have fetched an image already.
         */
        if ($album_has_existed) {
          $wrong[] = "$error_string Album existed before";
          continue;
        }

        /**
         * We can use this mbid to fetch the album cover (if it exists xoxo).
         * @var string
         */
        $album_mbid = $album["id"];
        $album_art_url = self::curl_album_art_redirect_url($album_mbid);

        if (!$album_art_url) {
          $wrong[] = "$error_string No album art";
          continue;
        }

        $album_cover_response = self::curl_album_art($album_art_url);

        if (!$album_cover_response || !isset($album_cover_response["images"][0]["image"])) {
          $wrong[] = "$error_string No album art image";
          continue;
        }

        /**
         * @var string
         */
        $image_url = $album_cover_response["images"][0]["image"];

        /**
         * @var string
         */
        $file_name = Utils::random_alpha_token(18);

        $client = new Client([
          'headers' => ['User-Agent' => 'BruderTrackzz/1.0 (cool@heia.kim)', 'Accept' => 'image/*']
        ]);

        $client->get(
          $image_url,
          ['sink' => ROOT . "/public/data/user/1/art/$file_name.jpg"]
        );

        $Album->art = "$file_name.jpg";
        $Album->save();
      } catch (\Exception $e) {
        $wrong[] = "$error_string :: " . $e->getMessage();
      }

    endforeach;

    return $this->success(data: $wrong);
  }
}
