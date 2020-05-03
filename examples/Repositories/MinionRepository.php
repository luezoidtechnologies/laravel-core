<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24/4/20
 * Time: 23:33 PM
 */

namespace Luezoid\Repositories;

use Luezoid\Laravelcore\Repositories\EloquentBaseRepository;
use Luezoid\Models\Minion;

class MinionRepository extends EloquentBaseRepository
{
    public $model = Minion::class;
}
