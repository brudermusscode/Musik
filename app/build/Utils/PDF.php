<?php

namespace Bruder\Utils;

class PDF
{
  public static function html_start()
  {
    return <<<HTML
      <!doctype html>
      <html>
        <head>
          <meta charset="utf-8" />
          <meta name="viewport" content="width=device-width, initial-scale=1" />

          <style>
            * {
              font-family: "Roboto", sans-serif !important;
            }

            [fl] {
              display: flex;
            }

            [jucsb] {
              justify-content: space-between;
            }

            [jucc] {
              justify-content: center;
            }

            [fldircol] {
              flex-direction: column;
            }

            [alistart] {
              align-items: flex-start;
            }

            [alic] {
              align-items: center;
            }

            [bold] {
              font-weight: bold;
            }

            * {
              margin: 0;
              padding: 0;
            }

            [lt] {
              float: left;
            }

            [rt] {
              float: right;
            }

            [cl] {
              clear: both;
            }

            [inline] {
              display: inline-block;
              vertical-align: top;
            }
          </style>
        </head>

        <body style="font-size: 14px; color: #333">
    HTML;
  }

  public static function html_end()
  {
    return <<<HTML
        </body>
      </html>
    HTML;
  }
}
