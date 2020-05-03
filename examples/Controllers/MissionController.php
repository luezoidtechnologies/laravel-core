<?php

namespace Luezoid\Http\Controllers;

use Illuminate\Http\Request;
use Luezoid\Http\Requests\MissionCreateRequest;
use Luezoid\Jobs\MissionCreateJob;
use Luezoid\Laravelcore\Http\Controllers\ApiController;
use Luezoid\Repositories\MissionRepository;

class MissionController extends ApiController
{
    protected $repository = MissionRepository::class;

    /**
     * A custom POST route handler triggering an Event via Job
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function createMission(Request $request)
    {
        $this->customRequest = MissionCreateRequest::class;
        return $this->handleCustomEndPoint(MissionCreateJob::class, $request);  // Calling custom handler function with a Custom Job specifically created to trigger an Event
    }
}
