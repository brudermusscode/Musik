<?php

$__log_path = _env("LOG_PATH") . "/php_errors.log";

/**
 * Standard ini values for error logging.
 */
ini_set("log_errors", 1);
ini_set("error_log", $__log_path);
error_reporting(E_ALL & ~E_DEPRECATED);

/**
 * Set error displaying based on current evnironment.
 */
if (current_env() == "dev" && !_env("STAGING")) {
  ini_set("display_errors", 1);
  ini_set("display_startup_errors", 1);
} else {
  ini_set("display_errors", 0);
  ini_set("display_startup_errors", 0);
}

/**
 * Reformat the php exception message.
 */
set_exception_handler(function ($ex) {
  $type = get_class($ex);
  $time = date('d.m.Y<;>H:i:s');

  /**
   * Log to the default log file defined for the local environment.
   */
  error_log("\n$time<;>$type<;>{$ex->getMessage()}<;>{$ex->getFile()}:{$ex->getLine()}<;>{$ex->getTraceAsString()}<&>\n");

  /**
   * Only show the exceptions in dev env.
   */
  if (current_env() !== "dev" && !_env("STAGING"))
    return;

  /**
   * @var string
   */
  $stacktrace = $ex->getTraceAsString();

  /**
   * @var bool
   */
  $app_init = defined("APP_INIT") && APP_INIT === true;

  /**
   * Start the outpout buffer and include the main styles if the
   * app is not yet completly initialized. Otherwise fallback to
   * an empty string, because we already have the main styles included.
   */
  ob_start();

  /**
   * Any js and css file.
   */
  include _root() . "/app/templates/global/_yield-requires.php";


  $include_styles = $app_init ? "" : ob_get_clean();

  /**
   * Clean the output buffer if the app is initialized.
   */
  if ($app_init) ob_end_clean();

  /**
   * Return the nice newly formatted exception screen!
   */
  echo <<<HTML
  $include_styles

  <exception-container>
    <inside>
      <container>
        <p text wider semibold tac mb32>$type</p>

        <div class=inner rd38 p12>
          <div p24 style="margin-bottom:-12px">
            <div fl gap12 alic>
              <mi>bug_report</mi>
              <p text smol ttup bold>Description</p>
            </div>
          </div>

          <div>
            <div style="background: white;" pblock24 pinline28 rd24 mblock12>
              <p text midler style="font-family:'Times New Roman', serif;">{$ex->getMessage()}</p>
            </div>
          </div>

          <div class=bottom rd32 fl gap24 alic jucsb>
            <p text>{$ex->getFile()}</p>
            <p class=line text semibold>
              <mi mid>water</mi>
              &nbsp;:{$ex->getLine()}
            </p>
          </div>
        </div>
      </container>

      <container>
        <div style=background:#de4832;color:#ffecee; class=inner rd38 p32 gap12>
          <div fl gap12 mb24 alic>
            <mi>stacks</mi>
            <p text smol ttup bold>Stacktrace</p>
          </div>

          <pre text smol>$stacktrace</pre>

        </div>
      </container>

      <p tac text smoler slight mt12>
        <a extern target="_blank" href="https://www.freepik.com/free-vector/flat-design-no-data-illustration_47718913.htm#fromView=search&page=1&position=7&uuid=07584568-95ce-4056-9350-7732fa2d910d&query=flat+error">Images by freepik</a>
      </p>
    </inside>


    <exception-image style="background: url('/assets/images/bruder/exception.svg');"></exception-image>
  </exception-container>
  HTML;
});

/**
 * Reformat the php exception message.
 */
set_error_handler(function (
  int $errno,
  string $errstr,
  ?string $errfile = null,
  ?int $errline = null,
  ?array $errcontext = null
) {
  /**
   * @var string
   */
  $type = match ($errno) {
    E_ERROR => "PHP Error",
    E_NOTICE => "PHP Notice",
    E_WARNING => "PHP Warning",
    E_PARSE => "PHP Parse Error",
    default => "Unknown Error Type"
  };

  /**
   * @var string
   */
  $time = date("d.m.Y<;>H:i:s");

  /**
   * Log to the default log file defined for the local environment.
   */
  error_log("\n{$time}<;>{$type}<;>{$errstr}<;>{$errfile}:{$errline}<&>\n");

  /**
   * Only show the errors in dev env.
   */
  if (current_env() !== "dev" && !_env("STAGING"))
    return;

  echo "<span><strong>{$type}</strong><br> $errstr<br>📁 {$errfile}:{$errline}</span>";
});
