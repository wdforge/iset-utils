<?php

namespace Iset\Utils;

/**
 * Class StaticScope
 * @package Iset\Utils
 */
class StaticScope
{
  /**
   * @var array
   */
  private static $_data;

  /**
   * @param $name
   * @return null
   */
  public static function get($name)
  {
    return isset(static::$_data[$name]) ? static::$_data[$name] : null;
  }

  /**
   * @param $name
   * @param $value
   */
  public static function set($name, $value)
  {
    static::$_data[$name] = $value;
  }

}