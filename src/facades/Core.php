<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 12/15/17
 * Time: 4:20 PM
 */

namespace Luezoid\Laravelcore\Facades;



use Illuminate\Support\Facades\Facade;

class Laravelcore extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'luezoid-laravel-core';
    }
}