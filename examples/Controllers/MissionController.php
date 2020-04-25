<?php

namespace Luezoid\Http\Controllers;

use Luezoid\Http\Requests\MissionCreateRequest;
use Luezoid\Jobs\MissionCreateJob;
use Luezoid\Repositories\MissionRepository;
use Illuminate\Http\Request;
use Luezoid\Laravelcore\Http\Controllers\ApiController;

class MissionController extends ApiController
{
    protected $repository = MissionRepository::class;

    public function createMission(Request $request)
    {
        $this->customRequest = MissionCreateRequest::class;
        return $this->handleCustomEndPoint(MissionCreateJob::class, $request);
    }
}
