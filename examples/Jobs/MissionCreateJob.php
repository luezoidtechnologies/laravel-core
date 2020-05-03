<?php

namespace Luezoid\Jobs;

use Luezoid\Events\BringMinionToLabEvent;
use Luezoid\Laravelcore\Jobs\BaseJob;
use Luezoid\Repositories\MissionRepository;

class MissionCreateJob extends BaseJob
{
    public $method = 'createMission';
    public $repository = MissionRepository::class;
    public $event = BringMinionToLabEvent::class;
}
