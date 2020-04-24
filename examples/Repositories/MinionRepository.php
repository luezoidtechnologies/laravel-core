<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24/4/20
 * Time: 23:33 PM
 */

namespace App\Repositories;

use App\Models\Minion;
use Luezoid\Laravelcore\Repositories\EloquentBaseRepository;

class MinionRepository extends EloquentBaseRepository
{
    public $model = Minion::class;
}
