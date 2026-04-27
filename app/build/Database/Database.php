<?php

namespace Bruder\Database;

/**
 * Using the Laravel Eloquernt ORM for easy m8.
 */

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
  public function __construct(?string $connection = null)
  {
    /**
     * In the file /config/global.php lies all your databse
     * confirguration. Visit it and fill it out, create a database
     * and user corresponding to it and you are good to go!
     */
    $config = require _root() . "/config/global.php";

    $capsule = new Capsule;
    $capsule->addConnection($config["database"][$connection ?? "default"]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
  }
}
