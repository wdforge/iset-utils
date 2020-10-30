<?php

namespace Iset\Utils;

/**
 * Class FileList
 * @package Iset\Utils
 */
class FileList
{
  /**
   * @param $directory
   * @param string $mask
   * @param callable|null $prepared
   * @return array|null|string|string[]
   */
  public static function fromPath($directory, $mask = '/[A-Za-z0-9\-\.а-я]+\.php$/u', callable $prepared = null)
  {
    $files = [];
    if (is_dir($directory)) {

      if ($handle = opendir($directory)) {

        while (($file = readdir($handle)) !== false) {
          if ($file == '.' || $file == '..')
            continue;
          $files[] = $file;
        }

        $files = preg_filter($mask, $directory . '/$0', $files);
        closedir($handle);
      }


      if ($prepared) {
        $files = array_map($files, $prepared);
      }
    }

    return $files;
  }
}