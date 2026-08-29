<?php

namespace Bruder\Model;

use Bruder\Bruder;
use Bruder\Utils\Utils;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Intervention\Image\Interfaces\ImageInterface;

class Artist extends Bruder
{

  const string ICON = "artist";
  const string COLOR = "secondary";

  /**
   * @var array
   */
  protected $fillable = [
    "name",
    "deleted_at",
    "updated_at",
  ];


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
      // Nothing hihi
    }

    /**
     * ? Portrait
     */
    if (!empty($params->art["tmp_name"])) {

      /**
       * @var object
       */
      $upload = $this->upload_art($params->art);

      if (!$upload->status)
        return error($upload->message);

      $this->art = $upload->data->file_name;
    }

    $this->db_transaction();

    try {

      $this->save();
      $this->db_commit();

      return success();
    } catch (\Exception $e) {
      $this->db_rollback();
      return error($e->getMessage());
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

    $save_path = _root() . "/public/data/user/1/portraits";
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
      while (Artist::where("art", $file_name)->first())
        $file_name = Utils::random_alpha_token(24) . ".webp";

      /**
       * @var string
       */
      $full_path = "$save_path/$file_name";

      // # Save!
      $encoded->save($full_path);

      return success(data: (object) ["file_name" => $file_name], json_encoded: false);
    } catch (\Exception $e) {

      /**
       * Delete the file if it exists already.
       */
      if (isset($full_path) && file_exists($full_path))
        unlink($full_path);

      return error($e->getMessage(), json_encoded: false);
    }
  }

  /**
   * @return HasMany<Album>
   */
  public function albums()
  {
    return $this->hasMany(Album::class);
  }

  public function all_albums()
  {

    $Albums = Album::where("artist_id", $this->id);

    $id = $this->id;
    $AlbumsThroughTracks = Album::whereHas("tracks.artistt", function ($q) use ($id) {
      $q->where("id", $id);
    });

    return $Albums->union($AlbumsThroughTracks)->get();
  }

  /**
   * @return HasMany<Track>
   */
  public function tracks()
  {
    return $this->hasMany(Track::class, "artist_id", "id");
  }

  /**
   * @return HasOne<Bookmark>
   */
  public function bookmark()
  {
    return $this->hasOne(Bookmark::class, "reference_id", "id");
  }

  /**
   * @return BelongsToMany<Artist>
   */
  public function genres()
  {
    return $this->belongsToMany(Genre::class, "artist_genres");
  }

  /**
   * @return Collection<Artist>
   */
  public function similiar_artists()
  {
    return $this->genres->first()
      ?->artists()
      ->whereNot("artist_id", $this->id)
      ->get() ?? Collection::empty();
  }

  /**
   * @return ?string
   */
  public function art_link()
  {
    return $this->art ? "/data/user/1/portraits/$this->art" : null;
  }
}
