<?php
/**
 * Created by PhpStorm.
 * User: choxx
 * Date: 10/07/19
 * Time: 12:58 PM
 */

namespace Luezoid\Laravelcore\Services;


class EnvironmentService
{
    private static $loggedInUserId;
    private static $loggedInUser;

    public function __construct()
    {
        //
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getLoggedInUserId()
    {
        if (is_null(self::$loggedInUserId)) {
            self::load();
        }
        return self::$loggedInUserId;
    }

    private static function load()
    {
        try {
            self::$loggedInUser = auth('api')->user();
            self::$loggedInUserId = self::$loggedInUser->id ?? null;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getLoggedInUser()
    {
        if (is_null(self::$loggedInUser)) {
            self::load();
        }
        return self::$loggedInUser;
    }
}