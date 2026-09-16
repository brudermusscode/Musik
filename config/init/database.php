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

# Here we check, if new migrations have to be applied.
$sql_dir = _root() . "/sql";
$last_migration_file_path =  "$sql_dir/last_migration";
$last_migration = file_get_contents($last_migration_file_path);
$last_migration_sql_path = $last_migration ? trim($last_migration) : null;
$migrations = scandir($sql_dir);

foreach ($migrations as $key => $sql_path) {

  # Unset all files that are not .sql.
  if (!str_contains($sql_path, ".sql"))
    unset($migrations[$key]);
}

if ($last_migration) {
  foreach ($migrations as $key => $sql_path) {
    unset($migrations[$key]);

    if ($last_migration_sql_path !== $sql_path) {
      continue;
    } else
      break;
  }
}

/**
 * Use a seperate try for just connecting as it might take some
 * time to establish it when mysql is still booting up. So you
 * know to just reload the page in some seconds.
 */
if ($migrations) :
  try {

    /**
     * @var PDO
     */
    $pdo = new PDO("mysql:host=$host", $user, $pass);
  } catch (\PDOException $e) {
    include _root() . "/app/templates/error/one-moment.php";
    exit();
  }

  try {

    # Create the database.
    $sql = "CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci; USE $db;";
    $pdo->exec($sql);

    # PDO::exec() commits automatically, so we need to turn it off
    # as we want to process everything in one transaction.
    $pdo->exec("SET autocommit = 0");

    $pdo->beginTransaction();

    $file_name = null;

    # Apply migrations.
    foreach ($migrations as $key => $sql_file) :
      $content = file_get_contents("$sql_dir/$sql_file");

      if (!$content) continue;

      $pdo->exec($content);
      $file_name = $sql_file;
    endforeach;

    if ($pdo->inTransaction())
      $pdo->commit();

    unset($pdo);

    # Set the last migration's file name to the last_migration so this script won't run
    # a second time.
    if ($file_name) {
      file_put_contents($last_migration_file_path, $file_name);

      echo "Alles erstellt, Seite wird neu geladen mein Freund!";

      # Reload the page! Everything should be fine now.
      header("location: /");
    }
  } catch (\PDOException $e) {

    if (isset($pdo)) {
      if ($pdo->inTransaction())
        $pdo->rollBack();

      unset($pdo);
    }

    die($e);
  }
endif;
