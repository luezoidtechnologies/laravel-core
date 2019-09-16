<?php
/**
 * Created by PhpStorm.
 * User: manoj
 * Date: 7/12/16
 * Time: 5:09 PM
 */

namespace Luezoid\Laravelcore\Exceptions;


class ServiceNotImplementedException extends \Exception
{
    public function __construct($message, $code = 501)
    {
        parent::__construct($message, $code);
    }
    
}