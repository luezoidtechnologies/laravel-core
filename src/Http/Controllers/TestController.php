<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 12/15/17
 * Time: 4:09 PM
 */

namespace Luezoid\Laravelcore\Http\Controllers;


use Illuminate\Http\Request;
use Luezoid\Laravelcore\Jobs\User\CreateJob;

class TestController extends ApiController
{

    public function store(Request $request)
    {
//        if ($this->storeRequest && $response = $this->validateRequest($this->storeRequest)) {
//            return $response;
//        }


        $data = [];


        $result = $this->dispatch(new CreateJob(compact('data')));

        return $this->standardResponse($result);

    }

}