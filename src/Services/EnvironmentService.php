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
    private static $isLoaded = false;

    public function __construct()
    {
        //
    }

    /**
     * @return null|integer
     */
    public static function getLoggedInUserId()
    {
        if (!self::$isLoaded) {
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
            // declaring the variables as null as they are already loaded
            self::$loggedInUserId = self::$loggedInUser = null;
        } finally {
            self::$isLoaded = true;
        }
    }

    /**
     * @return null|object
     */
    public static function getLoggedInUser()
    {
        if (!self::$isLoaded) {
            self::load();
        }
        return self::$loggedInUser;
    }
}
