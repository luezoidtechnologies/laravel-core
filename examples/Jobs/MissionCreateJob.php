<?php

namespace Luezoid\Jobs;

use Luezoid\Events\BringMinionToLabEvent;
use Luezoid\Repositories\MissionRepository;
use Luezoid\Laravelcore\Jobs\BaseJob;

class MissionCreateJob extends BaseJob
{
    public $method = 'createMission';
    public $repository = MissionRepository::class;
    public $event = BringMinionToLabEvent::class;
}
