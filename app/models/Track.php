<?php

namespace Bruder\Model;

use Illuminate\Support\Collection;
use Bruder\Bruder;
use Bruder\Http\Request;
use Bruder\Utils\Utils;
use getID3;

class Track extends Bruder
{

  const string ICON = "genres";
  const string COLOR = "primary";

  /**
   * @var array
   */
  protected $fillable = [
    "artist_id",
    "user_id",
    "file_name",
    "artist",
    "title",
    "genre",
    "year",
    "mime",
    "length_seconds",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @param object $params
   * @return string
   */
  public function edit(object $params)
  {

    /**
     * ? Listens
     */
    if (isset($params->listens) && $params->listens == 1)
      $this->listens += 1;

    /**
     * ? Video
     */
    if (!empty($params->video)) {

      /**
       * Upload new.
       */
      if (is_array($params->video)) {

        $upload = $this->upload_video($params->video);

        // ! Upload failed
        if (!$upload->status) return error($upload->message);

        $this->video = $upload->data->file_name;
      }

      /**
       * Delete the current.
       */
      else if ($params->video === "delete") {
        $file_path = _root() . "/public/data/user/1/videos/" . $this->video;

        if (file_exists($file_path))
          unlink($file_path);

        $this->video = null;
      }
    }

    /**
     * ? Title
     */

    $this->save();

    return success(data: $this);
  }

  /**
   * @return string
   */
  public function remove()
  {
    /**
     * Move the actual file to tracks/deleted/.
     */
    $path = _root() . "/public/data/user/1/tracks";
    $full_path = "$path/$this->file_name";


    $this->db_transaction();

    try {
      if (file_exists($full_path)) {

        /**
         * Serialize the file name by exploding it by a slash to
         * get the file name without the sub directory names.
         */
        $file_name = explode("/", $this->file_name);
        $file_name = array_last($file_name);

        /**
         * Rename will move the file to the deleted directory.
         */
        rename($full_path, "$path/deleted/" . $file_name);
      }

      $this->album_track()->delete();
      $this->playlist_tracks()->delete();
      $this->delete();
      $this->db_commit();
      return success();
    } catch (\Exception $e) {
      $this->db_rollback();
      return error($e->getMessage());
    }

    return success();
  }

  /**
   * Uploads a new file with a random alpha string as name and
   * encodes it as webp.
   *
   * @param array $file
   * @return object *->data holds the filename
   */
  public function upload_video(array $file)
  {

    $save_path = _root() . "/public/data/user/1/videos";
    $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
    $file_name = Utils::random_alpha_token(24) . "." . $extension;
    $final_path = $save_path . "/" . $file_name;

    /**
     * Die on any error.
     */
    $this->upload_error($file);

    try {

      move_uploaded_file($file["tmp_name"], $final_path);

      return success(data: (object) ["file_name" => $file_name], json_encoded: false);
    } catch (\Exception $e) {
      if (isset($save_path, $filename) && file_exists("$save_path/$filename"))
        unlink("$save_path/$filename");

      return error($e->getMessage(), false);
    }
  }

  /**
   * Dies on any upload error.
   *
   * @param array $file
   * @param bool $json_encoded
   * @return bool
   */
  public function upload_error(array $file, bool $json_encoded = true)
  {
    $error = match ($file["error"]) {
      default => false,
      UPLOAD_ERR_OK => false,
      UPLOAD_ERR_INI_SIZE => "Datei zu groß (php.ini)",
      UPLOAD_ERR_FORM_SIZE => "Datei zu groß (HTML-Form)",
      UPLOAD_ERR_PARTIAL => "Upload unvollständig",
      UPLOAD_ERR_NO_FILE => "Keine Datei hochgeladen",
      UPLOAD_ERR_NO_TMP_DIR => "Temp-Ordner fehlt",
      UPLOAD_ERR_CANT_WRITE => "Kann Datei nicht schreiben",
      UPLOAD_ERR_EXTENSION => "Upload durch Extension gestoppt",
    };

    return $error ? die(error($error, json_encoded: $json_encoded)) : false;
  }

  /**
   * Passing the path to a file will analyze this file and extract
   * possible metadata to create a new track in the database.
   *
   * @param string $path
   * @return string
   */
  public static function hhhhhhi89999999999999999999pcreate_from_file(array $files)
  {

    $log_path = ROOT . "/storage/logs/track_sync.log";
    $base_path = self::base_path();

    /**
     * @var Request
     */
    $Request = (new Request);

    /**
     * @var Track
     */
    $Object = new self;
    $Object->db_transaction();

    try {

      $error = [];
      $new = 0;

      foreach ($files as $path) :

        /**
         * The full path to the file going from root directory.
         * @var string
         */
        $file_path = "$base_path/$path";

        // ! File doesn't exist?
        if (!file_exists($file_path)) {
          array_push($error, "$path::doesn't exist");

          continue;
        }

        // ! Is directory?
        if (is_dir($file_path))
          continue;

        /**
         * Serialize the file name. The array of files contains
         * either only file names or a path going from the tracks
         * directory which are obviously seperated by a slash.
         */
        $file_name = explode("/", $path);
        $file_name = array_last($file_name);

        /**
         * Check if the file exists already.
         */
        if (Track::where("file_name", $path)->first())
          continue;

        /**
         * r = read
         * b = binary
         *
         * @var MpegAudio
         */
        if (!($file = fopen($file_path, "rb"))) {
          array_push($error, "$path::can't open requested file.");

          continue;
        }

        /**
         * @var resource $file
         */

        /**
         * @var string
         */
        $temp_file_path = tempnam("/tmp", "getID3");

        /**
         * Open the temporary file to be analyzed.
         */
        if (!($temp_file = fopen($temp_file_path, "wb"))) {
          array_push($error, "$path::fopen() for temp file failed.");
          fclose($file);

          continue;
        }

        /**
         * Write buffer from original file to temporary file.
         */
        while ($buffer = fread($file, 8192))
          fwrite($temp_file, $buffer);

        /**
         * Close temporary file.
         */
        fclose($temp_file);

        /**
         * Initialize the analyzation through getID3 library. Hopefully
         * this is bulletproof!
         */
        $file_analyzed = (new getID3)->analyze($temp_file_path);


        /**
         * Write to log file if adding fails and return.
         */
        if (isset($file_analyzed["error"]) && count($file_analyzed["error"]) > 0) {
          file_put_contents(
            $log_path,
            "$path::" . $file_analyzed["error"][0] . PHP_EOL,
            FILE_APPEND | LOCK_EX
          );

          fclose($file);
          array_push($error, "$path::failed adding track.");

          continue;
        }

        /**
         * Coming to here, the file is new and will be added in
         * the next steps.
         */
        file_put_contents($log_path, "Adding $path … ", FILE_APPEND | LOCK_EX);

        /**
         * @var array
         */
        $return_data = [
          "filesize" => $file_analyzed["filesize"],
          "fileformat" => $file_analyzed["fileformat"] ?? null,

          /**
           * Depending on the file format, the encoding can differ
           * alot so we need to take into account a variety of
           * arrays.
           */
          "info" => $file_analyzed["tags"]["id3v2"]
            ?? $file_analyzed["tags"]["id3v1"]
            ?? $file_analyzed["tags"]["vorbiscomment"]
            ?? null,

          /**
           * Playing & before the variable in a functions parameters
           * will create a direct reference to the original variable
           * and, when manipulated, will manipulate the original
           * variable, too.
           *
           * Here we pass the comments key of $file_analyzed to the
           * function with the & operator and set the picture key to
           * null, which will set the real value of the original array
           * to null, too! WOW!
           */
          "comments" => tap($file_analyzed["comments"] ?? [], function (&$c) {
            if (isset($c["picture"]))
              unset($c["picture"]);
          }),
          "mime" => $file_analyzed["mime_type"] ?? null,
          "length_seconds" => $file_analyzed["playtime_seconds"],
        ];

        /**
         * All done! We can delete the temporary file and close the
         * original source file.
         */
        unlink($temp_file_path);
        fclose($file);

        /**
         * Create a new track, populate the values and save it.
         * @var Track
         */
        $Track = new Track;

        # Adding the path as it contains the sub folder.
        $Track->file_name = $path;
        $Track->artist = $return_data["info"]["artist"][0] ?? null;
        $Track->title = $return_data["info"]["title"][0] ?? null;
        $Track->genre = $return_data["info"]["genre"][0] ?? null;
        $Track->year = $return_data["info"]["year"][0] ?? null;
        $Track->mime = $return_data["mime"] ?? null; // will return an error if null!
        $Track->length_seconds = $return_data["length_seconds"];

        /**
         * Get the Artist or create a new one.
         * @var ?Artist
         */
        if ($Track->artist) {
          $Artist = Artist::where("name", $Track?->artist)->first()
            ?? Artist::create([
              "name" => $Track->artist,
              "updated_at" => null,
            ]);

          $Track->artist_id = $Artist->id;
        }

        /**
         * # Save it!
         */
        $Track->save();

        file_put_contents($log_path, "»DONE«" . PHP_EOL, FILE_APPEND | LOCK_EX);

        $new++;
      endforeach;

      // # Commit all changes!
      $Object->db_commit();

      return $Request->success("Tracks synced! $new newly added" . ($error ? ", " . count($error) . " failed." : "."), $error);
    } catch (\Exception $e) {
      $Object->db_rollback();

      file_put_contents($log_path, "[" . $e->getMessage() . "]" . PHP_EOL, FILE_APPEND | LOCK_EX);

      return $Request->error($e->getMessage(), $e);
    }
  }

  /**
   * @return string
   */
  public static function base_path()
  {
    return _root() . "/public/data/user/1/tracks";
  }

  /**
   * @return ?User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return ?AlbumTrack
   */
  public function album_track()
  {
    return $this->hasOne(AlbumTrack::class);
  }

  /**
   * @return ?Album
   */
  public function albums()
  {
    return $this->hasManyThrough(Album::class, AlbumTrack::class, "track_id", "id", "id", "album_id");
  }

  /**
   * Using a second t at the end of the function name because I
   * have created the table with artist as a property. This
   * interferes with the function call and returns just the name,
   * not an Eloquent Model Instance.
   *
   * @return Artist
   */
  public function artistt()
  {
    return $this->belongsTo(Artist::class, "artist_id", "id");
  }

  /**
   * @return ?Collection<PlaylistTrack>
   */
  public function playlist_tracks()
  {
    return $this->hasMany(PlaylistTrack::class);
  }

  /**
   * @return ?string
   */
  public function art_link()
  {
    $album_art = $this->albums->first()?->art;
    $artist_art = $this->artistt?->art;

    return ($album_art ? "/data/user/1/art/$album_art" : (
      $artist_art ? "/data/user/1/portraits/$artist_art" : null
    ));
  }

  /**
   * @return ?string
   */
  public function video_link()
  {
    return $this->video ? "/data/user/1/videos/$this->video" : null;
  }

  /**
   * Returns only the minutes. Unsure if I want to show minutes:seconds.
   * @return string => 3:42
   */
  public function length_formatted()
  {
    $minutes = floor($this->length_seconds / 60);

    return $minutes;
  }
}
