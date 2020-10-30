<?php

namespace Iset\Utils;

use Illuminate\Database\Capsule\Manager as Capsule;
use Iset\Di\IDepended;
use Phinx\Migration\AbstractMigration;

/**
 * @method \Iset\Di\Manager getServiceManager()
 */
class Migration extends AbstractMigration implements IInitial, IDepended {
  /** @var \Illuminate\Database\Capsule\Manager $capsule */
  public $capsule;
  /** @var \Illuminate\Database\Schema\Builder $capsule */
  public $schema;

  public function init(IParams $params) {

    $config = $this->getServiceManager()->get('config')->get('database');
    $this->capsule = new Capsule;
    $this->capsule->addConnection($config);
    $this->capsule->bootEloquent();
    $this->capsule->setAsGlobal();
    $this->schema = $this->capsule->schema();
  }

  public function __call($name, $arguments)
  {
    // TODO: Implement @method \Iset\Di\Manager getServiceManager()
  }
}