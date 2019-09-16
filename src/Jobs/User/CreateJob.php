<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 12/18/17
 * Time: 1:46 PM
 */

namespace Luezoid\Laravelcore\Jobs\User;


use Luezoid\Laravelcore\Jobs\Job;
use Luezoid\Laravelcore\Repositories\User\UserRepository;

class CreateJob extends Job
{
    /**
     * data
     *
     * @var array
     */
    public $data;

    /**
     * BaseJob constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }


    public function handle(UserRepository $userRepository)
    {
        $item = $userRepository->create($this->data);

        return $item;
    }

}