<?php
/**
 * Created by PhpStorm.
 * User: keshav
 * Date: 22/12/18
 * Time: 6:15 PM
 */

namespace Luezoid\Laravelcore\Exceptions;


use Throwable;

class InvalidCredentialsException extends \Exception
{

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}