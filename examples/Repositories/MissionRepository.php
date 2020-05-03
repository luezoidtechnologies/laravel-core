<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25/4/20
 * Time: 8:32 PM
 */

namespace Luezoid\Repositories;


use Luezoid\Laravelcore\Repositories\EloquentBaseRepository;
use Luezoid\Models\Mission;

class MissionRepository extends EloquentBaseRepository
{
    public $model = Mission::class;

    /**
     * @param $data
     * @return mixed
     */
    public function createMission($data)
    {
        // do some business logic here
        $data['data']['lead_by_id'] = $data['data']['minion_id'];
        return parent::create($data);
    }
}
