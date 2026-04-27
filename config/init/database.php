<?php

$host = _env("MYSQL_HOST");
$user = _env("MYSQL_USER");
$pass = _env("MYSQL_PASSWORD");
$db   = _env("MYSQL_DATABASE");

/**
 * As of the 27th of April 2026, I have developed this app
 * without a public version in mind. This just checks if the base
 * database structure is in place. An automatic updating system
 * will be implemented.
 */
if (file_get_contents(_root() . "/sql/last_migration") !== "009_create_1st_user.sql") :
  try {
    /**
     * @var PDO
     */
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $sql = "CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci; USE $db;";
    $pdo->exec($sql);

    /**
     * Apply migrations.
     */
    $pdo->beginTransaction();
    $pdo->exec("SET autocommit = 0");

    $file_name = null;
    $sql_dir = _root() . "/sql";

    foreach (scandir($sql_dir) as $sql_file) :
      if (!str_contains($sql_file, ".sql"))
        continue;

      $pdo->exec(file_get_contents("$sql_dir/$sql_file"));
      $file_name = $sql_file;
    endforeach;

    $pdo->commit();

    file_put_contents("$sql_dir/last_migration", $file_name);

    unset($pdo);
  } catch (\PDOException $e) {

    if ($pdo->inTransaction()) {
      $pdo->rollBack();
      unset($pdo);
    }

    die($e);
  }
endif;
