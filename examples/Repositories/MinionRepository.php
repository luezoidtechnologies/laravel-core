<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24/4/20
 * Time: 23:33 PM
 */

namespace Luezoid\Repositories;

use Luezoid\Models\Minion;
use Luezoid\Laravelcore\Repositories\EloquentBaseRepository;

class MinionRepository extends EloquentBaseRepository
{
    public $model = Minion::class;
}
