<?php

return [

  "database" => [

    /**
     * This is the default configuration for your database
     * connection, while using the eloquent ORM of Laravel. Sp
     * powerful! I love it
     */
    "default" => [
      'driver' => 'mysql',
      'host' => _env("MYSQL_HOST"),
      'port' => _env("MYSQL_PORT"),
      'database' => _env("MYSQL_DATABASE"),
      'username' => _env("MYSQL_USER"),
      'password' => _env("MYSQL_PASSWORD"),
      'charset' => _env("MYSQL_CHARSET"),
      'collation' => _env("MYSQL_COLLATION"),
      'prefix' => '',
    ],

    /**
     * When running PHP scripts from the command line, which
     * include a database connection to this project, we need a
     * new connection. The MYSQL_HOST_CLI should be the IP address
     * of your mysql docker container containing the database you
     * want to interact with.
     */
    "CLI" => [
      'driver' => 'mysql',
      'host' => _env("MYSQL_HOST_CLI"),
      'port' => _env("MYSQL_PORT"),
      'database' => _env("MYSQL_DATABASE"),
      'username' => _env("MYSQL_USER"),
      'password' => _env("MYSQL_PASSWORD"),
      'charset' => _env("MYSQL_CHARSET"),
      'collation' => _env("MYSQL_COLLATION"),
      'prefix' => '',
    ],

    /**
   * Add as many as you like and expand.
   */
  ],
];
