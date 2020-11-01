<?php

namespace Iset\Utils;

use Psr\Container\ContainerInterface;

/**
 * Class Params
 * @package Iset\Utils
 */
class Params implements IParams
{
  use TreeContainer;

  protected $_params;

  /**
   * Params constructor.
   * @param array $values
   */
  public function __construct(array $values = [])
  {
    $this->setArray($values);
  }

  /**
   *
   * @param null
   * @return array
   */
  public function toArray()
  {
    return $this->_params ? $this->_params : [];
  }

  /**
   *
   * @param array
   * @return array
   */
  public function setArray(array $array)
  {
    $this->_params = $array;
    return $this->_params ? $this->_params : [];
  }

  /**
   * @param $section
   * @param $default
   * @return mixed
   */
  public function get($section, $default = null)
  {
    $result = $this->getElementFromPatch($section, $this->_params);
    if (is_null($result)) {
      return $default;
    }

    return $result;
  }

  /**
   * @param $section
   * @return mixed
   */
  public function set($section, $value)
  {
    return $this->setElementToPatch($section, $this->_params, $value);
  }

  /**
   * @param $section
   * @return bool
   */
  public function uset($section)
  {
    return $this->deleteElementFromPatch($section, $this->_params);
  }

  /**
   * @return ContainerInterface
   */
  public function getContainer()
  {
    return new Container($this);
  }
}