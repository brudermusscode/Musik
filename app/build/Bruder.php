<?php

namespace Bruder;

use Bruder\Trait\ProcessesRequests;
use Illuminate\Database\Eloquent\Model;

class Bruder extends Model
{
  use ProcessesRequests;

  /**
   * @return ?object
   */
  public function data()
  {
    return $this->exists ? (object) $this->getAttributes() : null;
  }

  /**
   * @return string
   */
  public function current_timestamp()
  {
    return date("Y-m-d H:i:s", time());
  }

  /**
   * Path has to be appended with a trailing slash /.
   *
   * @param string $path
   * @return string
   */
  public function template(string $path)
  {
    return _root() . "/app/templates$path";
  }

  /** ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, */
  /** ,,,,,,,,,,,,,,,,,,, DATABASE INTERACTIONS ,,,,,,,,,,,,,,,,,,,, */
  /** ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, */

  /**
   * @return void
   * @throws \Throwable
   */
  public function db_transaction()
  {
    return $this->getConnection()
      ->beginTransaction();
  }

  /**
   * @return void
   * @throws \Throwable
   */
  public function db_commit()
  {
    return $this->getConnection()
      ->commit();
  }

  /**
   * @return void
   * @throws \Throwable
   */
  public function db_rollback()
  {
    return $this->getConnection()
      ->rollBack();
  }

  /**
   * @return
   */
  // TODO: Make this static & non-static
  public static function findOrReturn(mixed $id = null, ?string $die_message = null)
  {
    return static::find($id)
      ?? die((new self)->error($die_message ?? "<strong>Model Instance not found.</strong>"));
  }
}
