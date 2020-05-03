<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24/4/20
 * Time: 23:33 PM
 */

namespace Luezoid\Http\Controllers;

use Luezoid\Http\Request\MinionCreateRequest;
use Luezoid\Http\Request\MinionDeleteRequest;
use Luezoid\Http\Request\MinionUpdateRequest;
use Luezoid\Laravelcore\Http\Controllers\ApiController;
use Luezoid\Laravelcore\Jobs\BaseJob;
use Luezoid\Repositories\MinionRepository;

class MinionController extends ApiController
{
    protected $repository = MinionRepository::class;

    protected $createJob = BaseJob::class;
    protected $storeRequest = MinionCreateRequest::class;

    protected $updateJob = BaseJob::class;
    protected $updateRequest = MinionUpdateRequest::class;

    protected $deleteJob = BaseJob::class;
    protected $deleteRequest = MinionDeleteRequest::class;
}
