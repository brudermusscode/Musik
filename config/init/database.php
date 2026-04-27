<?php

/**
 * Programming in my bed, covered by a thick sheep wool blanket on my
 * Lenovo Yoga Pro 7. Life can be wonderful :=)
 *
 * * May The lord be with you my friend.
 */

$host = _env("MYSQL_HOST");
$user = _env("MYSQL_USER");
$pass = _env("MYSQL_PASSWORD");
$db   = _env("MYSQL_DATABASE");

/**
 * As of the 27th of April 2026, I have been developing this app
 * without a public version in mind. Now this just checks if the
 * base database structure is in place. An automatic updating
 * system will be implemented.
 */
// TODO: Implement basic mysql migration system.
if (file_get_contents(_root() . "/sql/last_migration") !== "009_create_1st_user.sql") :

  /**
   * Use a seperate try for just connecting as it might take some
   * time to establish it when mysql is still botting up. So you
   * know to just reload the page in some seconds.
   */
  try {

    /**
     * @var PDO
     */
    $pdo = new PDO("mysql:host=$host", $user, $pass);
  } catch (\PDOException $e) {
    echo "Konnte noch keine Verbindung herstellen. Versuch es nochmal in ein paar Sekunden!<br><br>";
    echo $e->getMessage();
  }

  try {

    /**
     * Create the database.
     */
    $sql = "CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci; USE $db;";
    $pdo->exec($sql);
    $pdo->beginTransaction();

    /**
     * PDO::exec() commits automatically, so we need to turn it off
     * as we want to process everything in one transaction.
     */
    $pdo->exec("SET autocommit = 0");

    $file_name = null;
    $sql_dir = _root() . "/sql";

    /**
     * Apply migrations.
     */
    foreach (scandir($sql_dir) as $sql_file) :
      if (!str_contains($sql_file, ".sql"))
        continue;

      $pdo->exec(file_get_contents("$sql_dir/$sql_file"));
      $file_name = $sql_file;
    endforeach;

    /**
     * # Commit!
     */
    $pdo->commit();


    /**
     * Set the last migration's file name to the last_migration so
     * this script won't run a second time.
     */
    $last_migration_file_path =  "$sql_dir/last_migration";
    file_put_contents($last_migration_file_path, $file_name);

    unset($pdo, $last_migration_file_path);
  } catch (\PDOException $e) {

    if (isset($pdo)) {
      if ($pdo->inTransaction())
        $pdo->rollBack();

      unset($pdo);
    }

    die($e);
  }

  echo "Alles erstellt, Seite wird neu geladen mein Freund!";

  /**
   * Reload the page! Everything should be fine now.
   */
  header("location: /");
endif;
