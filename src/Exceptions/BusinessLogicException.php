<?php
/**
 * Created by PhpStorm.
 * User: keshav
 * Date: 27/12/18
 * Time: 9:11 AM
 */

namespace Luezoid\Laravelcore\Exceptions;


class BusinessLogicException extends \Exception
{

    public function __construct($message, $code = 500)
    {
        parent::__construct($message, $code);
    }

}