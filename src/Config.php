<?php

namespace Iset\Utils;

/**
 * Class Manager
 * @package Iset\Config
 */
class Config extends Params
{

  /**
   * Метод загрузки массива путей конфигов
   *
   * @param array $configs
   * @return none
   */
  public function loadConfigs($configs)
  {
    // загрузка конфига или конфигов
    if (!empty($configs) && is_array($configs)) {
      foreach ($configs as $config_path) {
        $this->loadConfig($config_path);

        // вызов кода из конфига
        $this->callbackSection('_callback');
      }
    } elseif (!empty($configs) && is_string($configs)) {

      $this->loadConfig($configs);

      // вызов кода из конфига
      $this->callbackSection('_callback');
    }

    return $this;
  }


  /**
   * Метод для выполнения callback секции  в конфиге
   *
   * @param string|array $section
   * @param bool $drop
   *
   * @return string
   */
  public function callbackSection($section = '_callback', $drop = true)
  {
    $configSection = $this->get($section);

    if (is_array($configSection)) {
      foreach ($configSection as $call) {
        if (is_callable($call)) {
          $call($this);
        }
      }
    } elseif (isset($configSection) && is_callable($configSection)) {
      $configSection($this);
    }

    if (isset($configSection) && $drop) {
      $this->unset($section);
    }
  }


  /**
   * Загрузка нового конфиг-массива
   *
   * @param string $filePath
   * @return array
   */
  public function loadConfig($filePath)
  {
    if (file_exists($filePath) && is_readable($filePath)) {
      $new_config = include_once($filePath);
      if ($new_config && (is_array($new_config) || (is_object($new_config) && $new_config instanceof \ArrayAccess))) {
        $this->_params = static::mergeArray($this->toArray(), $new_config);
      }
    } else {
      throw new Exception('File config not found. Path: ' . $filePath);
    }

    return $this->toArray();
  }

  /**
   * Метод слияния массивов
   *
   * @param $Arr1
   * @param $Arr2
   * @return array
   */
  protected static function mergeArray(array $Arr1, array $Arr2)
  {
    $MergeArrays = function ($Arr1, $Arr2) use (&$MergeArrays) {
      foreach ($Arr2 as $key => $Value) {
        if (array_key_exists($key, $Arr1) && is_array($Value))
          $Arr1[$key] = $MergeArrays($Arr1[$key], $Arr2[$key]);
        else if (is_int($key))
          $Arr1[] = $Value;
        else
          $Arr1[$key] = $Value;

      }
      return $Arr1;
    };

    return $MergeArrays($Arr1, $Arr2);
  }

  /**
   * Cлияние конфига со стартовыми параметрами скрипта
   *
   * @param $argv
   * @return array
   */
  protected function mergeCommands($argv)
  {
    array_shift($argv);
    $out = array();
    foreach ($argv as $arg) {
      if (substr($arg, 0, 2) == '--') {
        $eqPos = strpos($arg, '=');
        if ($eqPos === false) {
          $key = substr($arg, 2);
          $out[$key] = isset($out[$key]) ? $out[$key] : true;
        } else {
          $key = substr($arg, 2, $eqPos - 2);
          $out[$key] = substr($arg, $eqPos + 1);
        }
      } else if (substr($arg, 0, 1) == '-') {
        if (substr($arg, 2, 1) == '=') {
          $key = substr($arg, 1, 1);
          $out[$key] = substr($arg, 3);
        } else {
          $chars = str_split(substr($arg, 1));
          foreach ($chars as $char) {
            $key = $char;
            $out[$key] = isset($out[$key]) ? $out[$key] : true;
          }
        }
      } else {
        $out[] = $arg;
      }
    }

    static::mergeArray(static::$_config, $out);
    return $out;
  }

  /**
   * Проксирование доступа через свойства к параметрам конфига
   *
   * @param $name
   * @return mixed|null
   */
  public function __get($name)
  {
    return isset(static::$_config[$name]) ? static::$_config[$name] : null;
  }

  /**
   * Проксирование выполнения несуществующих методов
   *
   * @param $name
   * @param $arguments
   * @return NullObject
   */
  public function __call($name, $arguments)
  {
    return NullObject::create($name);
  }


  public function __toString()
  {
    return var_export($this->_params, true);
  }

}