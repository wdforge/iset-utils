<?php

namespace Iset\Utils;

/**
 * Класс заглушка от некорректных вызовов вернутых значений
 * Если создан экземпляр этого класса значит произошло некорректное поведение
 * например:
 * $object->getOtherObject()->execute()
 * если getOtherObject вернет null это будет плохо, а если заглушку, то лучше.
 * Доп. как вариант улучшения информативности, вернуть из стека место вызова ну или кусок стека
 */

/**
 * Class NullObject
 * @package Iset\Utils
 */
class NullObject extends \SimpleXMLElement /* Hack for toBoolean conversion http://stackoverflow.com/questions/6113387/how-to-create-a-php-class-which-can-be-casted-to-boolean-be-truthy-or-falsy  */
{

  public $method = '';
  protected $file = '';
  protected $lineno = 0;


  public static function create($method = '')
  {
    return new NullObject($xml = '<!--' . htmlentities(serialize($method)) . "-->" . "<a></a>");
  }

  public function method()
  {
    preg_match("#<!\-\-(.+?)\-\->#", $this->asXML(), $matches);
    if (!$matches) return [];
    return unserialize(html_entity_decode($matches[1]));
  }

  function __call($name, $arguments)
  {

    if (ini_get('display_errors')) {
      debug_print_backtrace(10);
    }

    throw new \Exception('Not create object, and not running method: "' . $this->method() . '(' . var_export($arguments, true) . ')";');
  }

  function __set($name, $value)
  {
    throw new \Exception('Object undefined is not setting value ' . $name . '="' . var_export($value, true) . '" in ' . $this->method() . ';');
  }

  function __get($name)
  {
    throw new \Exception('Object undefined is not getting value "' . $name . '" in' . $this->method() . ';');
  }

  function __toString()
  {
    throw new \Exception('String undefined is not set value in ' . $this->method());
  }

  function __invoke($x)
  {
    throw new \Exception('Can not be executed, method is not defined in' . $this->method() . ' => ' . var_export($x, true));
  }

  function __unset($name)
  {
    throw new \Exception('Object undefined is not getting value "' . $name . '" in' . $this->method() . ';');
  }

}
