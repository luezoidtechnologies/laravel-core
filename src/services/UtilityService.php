<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 1/28/18
 * Time: 1:53 AM
 */

namespace Luezoid\Laravelcore\Service;


use Carbon\Carbon;
use Carbon\CarbonInterval;
use DatePeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Luezoid\Laravelcore\Rules\RequestSanitizer;

class UtilityService
{
    public $tableNameCaching = false;
    public $tableColCaching = false;

    public static function is_json($string, $return_data = false)
    {
        $data = json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE && $string && $string != $data) ? ($return_data ? $data : TRUE) : FALSE;
    }

    public static function fromCamelToSnake($inputs)
    {
        foreach ($inputs as $key => $input) {
            if (is_numeric($key)) {
                $newKey = $key;
            } else {
                $newKey = Str::snake($key);
            }
            unset($inputs[$key]);

            if (is_array($input)) {
                $inputs[$newKey] = self::fromCamelToSnake($input);
            } else {
                $inputs[$newKey] = $input;
            }
        }

        return $inputs;
    }

    public static function fromSnakeToCamel($inputs)
    {
        foreach ($inputs as $key => $input) {
            unset($inputs[$key]);
            $newKey = self::camel_case($key);
            if (is_array($input)) {
                $inputs[$newKey] = self::fromSnakeToCamel($input);
            } else {
                $inputs[$newKey] = $input;
            }
        }

        return $inputs;
    }

    public static function camel_case($key)
    {
        $newKey = $key;
        if (Str::contains($key, '_')) {
            $newKey = Str::camel($key);
        }
        return $newKey;
    }

    public static function getClassName($class)
    {
        $path = explode('\\', $class);
        return array_pop($path);

    }

    public static function getDays($dayName)
    {
        return new DatePeriod(
            Carbon::parse("first $dayName of this month"),
            CarbonInterval::week(),
            Carbon::parse("first $dayName of next month")
        );
    }

    public static function getRequestSanitizerNotRequiredRule($validationData = [])
    {
        $timestamp = time();
        return new RequestSanitizer($validationData, [
            ['keyName' => "key$timestamp", 'type' => 'value', 'value' => $timestamp]
        ]);
    }

    public static function getModelTableName(string $modelClass)
    {
        $cacheKey = 'LZD_TABLE_' . $modelClass;
        return Cache::remember($cacheKey, 600, function () use ($modelClass) {
            return (new $modelClass)->getTable();
        });
    }

    public static function getColumnsForTable(string $modelClass)
    {
        $cacheKey = 'LZD_TABLE_COL_' . $modelClass;
        return Cache::remember($cacheKey, 600, function () use ($modelClass) {
            return Schema::getColumnListing(self::getModelTableName($modelClass));
        });
    }
}
