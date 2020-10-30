<?php

namespace Iset\Utils;

use Iset\Utils\IParams;

/**
 * Interface IInitial
 * @package Iset\Utils
 */
interface IInitial
{
  /**
   * @param \Iset\Utils\IParams $params
   * @return mixed
   */
  public function init(IParams $params);
}