<?php
/**
 * Created by PhpStorm.
 * User: choxx
 * Date: 10/07/19
 * Time: 12:58 PM
 */

namespace Luezoid\Laravelcore\Services;


use Illuminate\Validation\Rule;

class RequestService
{
    /**
     * @param $model
     * @param string $column
     * @return \Illuminate\Validation\Rules\Exists
     */
    public static function exists($model, $column = 'id')
    {
        return Rule::exists(UtilityService::getModelTableName($model), $column);
    }

    /**
     * @param $model
     * @param string $column
     * @return \Illuminate\Validation\Rules\Unique
     */
    public static function unique($model, $column = 'id')
    {
        return Rule::unique(UtilityService::getModelTableName($model), $column);
    }
}