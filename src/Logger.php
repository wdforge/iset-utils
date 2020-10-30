<?php

namespace Iset\Utils;
/**
 * Class Logger
 * @package Iset\Utils
 */
class Logger
{
  /**
   * @var string
   */
  static public $logfile;

  /**
   * @param $log
   */
  static public function Log($log)
  {

    $dir = dirname(self::$logfile);

    if (!is_dir($dir)) {
      if (!mkdir($dir, 0777, true)) {
        echo sprintf("Не удаётся создать лог фаил: %s.", $dir);
      }

      if (!empty($log) && is_string($log) && !empty($logfile) && $file = fopen($logfile, "a")) {
        $date_str = date('Y-m-d H:i:s');
        fwrite($file, "\n" . $date_str . ":  " . $log . "\n");
        fclose($file);
      }
    }
  }
}
