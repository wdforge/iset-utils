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
    $mergeArrays = function ($configArray1, $configArray2) use (&$mergeArrays) {

      if (is_array($configArray1)) {
        foreach ($configArray2 as $key => $value) {
          if (array_key_exists($key, $configArray1) && is_array($value))
            $configArray1[$key] = $mergeArrays($configArray1[$key], $configArray2[$key]);
          else if (is_int($key))
            $configArray1[] = $value;
          else
            $configArray1[$key] = $value;
        }
      }

      return $configArray1;
    };

    if (file_exists($filePath) && is_readable($filePath)) {
      $new_config = include_once($filePath);
      if ($new_config && (is_array($new_config) || (is_object($new_config) && $new_config instanceof \ArrayAccess))) {
        $this->_params = $mergeArrays($this->toArray(), $new_config);
      }
    } else {
      throw new Exception('File config not found. Path: ' . $filePath);
    }

    return $this->toArray();
  }
  public function __toString()
  {
    return var_export($this->_params, true);
  }

}