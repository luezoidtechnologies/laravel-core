<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 1/28/18
 * Time: 1:24 AM
 */

namespace Luezoid\Laravelcore\Exceptions;


class AppException extends \Exception
{

    public function __construct($message, $code = 400)
    {
        parent::__construct($message, $code);
    }

}