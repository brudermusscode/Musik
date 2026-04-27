<?php

use Bruder\Application\Application;

/**
 * Environmental.
 */
define("ENV", _env());
define("DEV", ENV?->ENVIRONMENT === "dev");
define("STAGE", ENV?->ENVIRONMENT === "stage");
define("PROD", ENV?->ENVIRONMENT === "prod");

/**
 * Gernally useful.
 */
define("CURRENT_TIMESTAMP",  date("Y-m-d H:i:s", time()));
define('APP', new Application);
define("ROOT", _root());
define("PREROOT", dirname(ROOT));
define("VENDOR", ROOT . '/vendor');
define("CONFIG", ROOT . '/config');
define("ROUTE", ROOT . '/routes');
define("MODEL", ROOT . '/app/models');
define("BUILD", ROOT . '/app/build');
define("LOG", ENV?->LOG_PATH);
define("JSON_RESPONSE", 'Content-type: application/json');
define("HTML_RESPONSE", 'Content-type: text/html');
define(false, 0);
define(true, 1);

/**
 * Templates.
 */
define("TEMPLATE", ROOT . '/app/templates');
define("UNAVAILABLE", ROOT . '/app/templates/error/404.php');

/**
 * Base definitions.
 */
define("APP_NAME", ENV?->APP_NAME);
define('APP_VERSION', ENV?->APP_VERSION);
define('MAINTENANCE', ENV?->MAINTENANCE);
define("HOME_URL", ENV?->SERVER_ADDRESS);
define("IMAGE", HOME_URL . "/assets/images");
define("SCRIPT", HOME_URL . "/assets/js");
define("STYLE", HOME_URL . "/assets/css");
define("FONT", HOME_URL . "/assets/fonts");

/**
 * Pathing.
 */
define("ASSET", ROOT . "/public/assets");
