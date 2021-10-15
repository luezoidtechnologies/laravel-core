<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 12/18/17
 * Time: 1:12 PM
 */

namespace Luezoid\Laravelcore\Repositories\User;


use Luezoid\Laravelcore\Repositories\EloquentBaseRepository;

class UserRepository extends EloquentBaseRepository
{
    public function __construct()
    {
        $this->model = User::class;
    }


}