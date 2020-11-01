<?php

namespace Iset\Utils;

/**
 * Interface IParams
 * @package Iset\Utils
 */
interface IParams
{
  /**
   * @return mixed
   */
  public function toArray();

  /**
   * @param array $array
   * @return mixed
   */
  public function setArray(array $array);

  /**
   * @param $section
   * @param null $default
   * @return mixed
   */
  public function get($section, $default = null);

  /**
   * @param $section
   * @param $value
   * @return mixed
   */
  public function set($section, $value);

  /**
   * @param $section
   * @return mixed
   */
  public function uset($section);
}