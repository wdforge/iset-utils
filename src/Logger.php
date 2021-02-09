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
    $dir = realpath($dir);
    if (!is_dir($dir)) {
      if (!mkdir($dir, 0777, true)) {
        echo(sprintf("Не удаётся создать директорию лог фаила: %s.\n", $dir));
        return false;
      }
    }
    if (!is_writeable($dir) || !is_writable($dir)) {
      echo(sprintf("Отсутствуют права на запись: %s.\n", self::$logfile));
      return false;
    }
    if (!empty($log) && is_string($log) && !empty(self::$logfile)) {
      if ($file = fopen(self::$logfile, "a")) {
        chmod(self::$logfile, 0777);
        $dateStr = date('Y-m-d H:i:s');
        fwrite($file, "\n" . $dateStr . ":  " . $log . "\n");
        fclose($file);
      } else {
        echo(sprintf("Не удаётся записать лог фаил: %s.\n", $file));
        return false;
      }
    } else {
      echo "Что-то не так с именем лог файла: ".self::$logfile."\n";
      return false;
    }
    return true;
  }
}
