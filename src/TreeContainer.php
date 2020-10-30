<?php

namespace Iset\Utils;

/**
 * Trait TreeContainer
 * @package Iset\Utils
 */
trait TreeContainer
{
  /**
   * @param array $pathArray
   *
   * @return array
   * приведение маршрута-массива к форматированной строке ["element"]["element"]["element"].
   *
   */
  protected function linePath($pathArray = [])
  {
    $result = '';

    foreach ($pathArray as $pathLine) {
      if (is_scalar($pathLine)) {
        $result .= '["' . $pathLine . '"]';
      }
    }

    return $result;
  }

  /**
   * @param array|string $path
   *
   * @return array
   **@todo приведение строкового маршрута к массиву.
   *
   */
  protected function normalizePath($path)
  {
    if (isset($path) && !empty($path) && is_scalar($path)) {

      while (mb_strpos($path, '//') !== false) {
        $path = str_replace('//', '/', $path);
      }

      $path = ($path[0] == '/') ? mb_substr($path, 1, mb_strlen($path)) : $path;
      $path = ($path[mb_strlen($path) - 1] == '/') ? mb_substr($path, 0, mb_strlen($path) - 1) : $path;

      if (is_string($path) && mb_strpos($path, '/') !== false) {
        return explode('/', $path);
      } elseif (is_scalar($path)) {
        return [$path];
      }
    }

    return $path;
  }

  /**
   * @param array|string $path
   * @param array $values
   *
   * @return mixed
   **@todo вызов callback по маршруту в виде массива:
   * ["element", "element", "element"], либо в виде строки с разделителями "/element/element/element",
   * допускаются комплексные варианты и повторы разделителей.
   *
   */
  protected function callbackToPath($path, &$values, callable $callback, $params = [], /* recursive flags: */
                                    $pathPosition = 0, $usePath = [], &$originValues = [])
  {
    if (empty($path)) {
      return;
    }

    $originValues = empty($originValues) ? $values : $originValues;
    $path = $this->normalizePath($path);

    if (is_scalar($path) && isset($values[$path])) {
      $usePath[] = $path;
      return $callback($params, $values[$path], $originValues, $usePath);
    }

    if (!empty($path) && is_array($path)) {

      $sizePath = count($path) - 1;

      while ($pathToken = current($path)) {

        $usePath[] = $pathToken;
        if ($sizePath == $pathPosition && isset($values[$pathToken])) {

          return $callback($params, $values[$pathToken], $originValues, $usePath);

        } elseif (isset($values[$pathToken]) && is_array($values[$pathToken])) {

          next($path);
          $pathPosition++;

          return $this->callbackToPath($path, $values[$pathToken], $callback, $params, $pathPosition, $usePath, $originValues);
        }

        next($path);
        $pathPosition++;
      }
    }

    return null;
  }


  /**
   * @param array|string $path
   * @param array $values
   *
   * @return mixed
   **@todo Обращение к массиву значений по маршруту в виде массива:
   * ["element", "element", "element"], либо в виде строки с разделителями "/element/element/element",
   * допускаются комплексные варианты и повторы разделителей.
   *
   */
  public function getElementFromPatch($path, array &$valuesArray = [])
  {
    $result = null;

    if (is_array($valuesArray) && $pathString = $this->linePath($this->normalizePath($path))) {
      eval("\$result = isset(\$valuesArray" . $pathString . ")?\$valuesArray" . $pathString . ":null;");
    }

    return $result;

    /* v2:
    return $this->callbackToPath($path, $values, (function($value, $values, array $usePath = []){
        return $value;
    }));
    */
  }

  /**
   * @param array|string $path
   * @param array $values
   * @param integer $pathPosition
   *
   * @return bool
   **@todo Удаление значения из массива значений по маршруту, в виде массива:
   * ["element", "element", "element"], либо в виде строки с разделителями "/element/element/element",
   * допускаются комплексные варианты и повторы разделителей.
   *
   */
  public function deleteElementFromPatch($path, &$valuesArray = [])
  {
    if ($pathString = $this->linePath($this->normalizePath($path))) {
      eval("unset(\$valuesArray" . $pathString . ");");
    }

    return $valuesArray;
  }

  /**
   * @param array|string $path
   * @param array $values
   * @param mixed $value
   * @param integer $pathPosition
   *
   * @return bool
   **@todo Установка значения в массиве значений по маршруту, в виде массива:
   * ["element", "element", "element"], либо в виде строки с разделителями "/element/element/element",
   * допускаются комплексные варианты и повторы разделителей.
   *
   */
  public function setElementToPatch($path, &$valuesArray = [], $newValue = null)
  {
    if ($pathString = $this->linePath($this->normalizePath($path))) {
      eval("\$valuesArray" . $pathString . "= \$newValue;");
    }

    return $valuesArray;
  }


  /**
   * @param array|string $path
   * @param array $values
   * @param mixed $value
   * @param integer $pathPosition
   *
   * @return bool
   **@todo Проверка наличия значения в массиве значений по маршруту, в виде массива:
   * ["element", "element", "element"], либо в виде строки с разделителями "/element/element/element",
   * допускаются комплексные варианты и повторы разделителей.
   *
   */
  public function hasElementFromPatch($path, &$values = [])
  {
    $result = false;
    $pathString = $this->linePath($this->normalizePath($path));
    eval("\$result = isset(\$values" . $pathString . ");");
    return $result;
  }
}
