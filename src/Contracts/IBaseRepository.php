<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 12/18/17
 * Time: 12:32 PM
 */

namespace Luezoid\Laravelcore\Contracts;

interface IBaseRepository
{
    public function create($data);

    public function update($data);

    public function delete($data);

    public function find($id, $params = null);

    public function findOrCreate($object);

    public function updateAll($condition, $update, $in = false);

    public function getAll($params = []);

    public function _checkInputValueType($value);

    public function filter($data, $first = false, $fields = []);

}
