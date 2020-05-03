<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 12/18/17
 * Time: 12:06 PM
 */

namespace Luezoid\Laravelcore\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Luezoid\Laravelcore\Contracts\IBaseRepository;
use Luezoid\Laravelcore\Exceptions\AppException;
use Luezoid\Laravelcore\Services\UtilityService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EloquentBaseRepository implements IBaseRepository
{
    const SELECT_FILTER_TYPE_WHERE_HAS = 'whereHas';
    const SELECT_FILTER_TYPE_WITH = 'with';
    /**
     * Model Class Name
     *
     * @var String;
     */
    public $model = '';
    protected $selectFilterType = self::SELECT_FILTER_TYPE_WHERE_HAS;

    protected $indexTransformer;
    protected $enableRelationFilters = true;    // by default RELATION filters are ON
    protected $enableSelectFilters = true;      // by default SELECT filters are ON

    /**
     * @param $data
     * @return null
     * @throws AppException
     */
    public function insert($data)
    {
        try {
            $items = (new $this->model())::insert($data['data']);

            if ($items) {
                return $data;
            }
            return null;
        } catch (\Exception $exception) {
            throw new AppException($exception->getMessage(), 500);
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function update($data)
    {
        $item = $this->find($data['id']);

        if (is_null($item)) {
            throw new NotFoundHttpException();
        }

        $item->update($data["data"]);

        if (method_exists($this, 'afterUpdate'))

            $this->afterUpdate($item, $data);

        return $item;
    }

    /**
     * @param $id
     * @param null $params
     * @return mixed
     */
    public function find($id, $params = null)
    {
        $query = null;
        $_model = new $this->model;
        $filterable = property_exists($_model, 'filterable') ? ($_model)->filterable : [];

        if (isset($params["with"]) && count($params["with"])) {
            $query = call_user_func_array([$this->model, 'with'], [$params['with']]);
        }

        if (isset($params["inputs"]) && $where = Arr::only((array)$params["inputs"], $filterable))

            $query = call_user_func_array([$query ? $query : $this->model, 'where'], [$where]);

        return call_user_func_array([$query ? $query : $this->model, 'find'], isset($params['columns']) ? [$id, $params['columns']] : [$id]);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function delete($data)
    {
        $object = $this->find($data['id']);

        if (is_null($object)) {
            throw new NotFoundHttpException();
        }

        $object->delete();

        if (method_exists($this, 'afterDelete'))

            $this->afterDelete($object);

        return $object;

    }

    /**
     * @param $id
     * @param null $params
     * @return mixed
     */
    public function show($id, $params = null)
    {
        $query = null;
        $_model = new $this->model;
        $filterable = property_exists($_model, 'filterable') ? ($_model)->filterable : [];

        if (isset($params["with"]) && count($params["with"])) {
            $query = call_user_func_array([$this->model, 'with'], [$params['with']]);
        }

        if (isset($params["inputs"]) && $where = Arr::only((array)$params["inputs"], $filterable))

            $query = call_user_func_array([$query ? $query : $this->model, 'where'], [$where]);

        $data = call_user_func_array([$query ? $query : $this->model, 'find'], isset($params['columns']) ? [$id, $params['columns']] : [$id]);

        if (is_null($data)) {
            throw new NotFoundHttpException();
        }

        return $data;
    }

    /**
     * @param $params
     * @param bool $first
     * @return mixed|null
     */
    public function findByParamValue($params, $first = true)
    {
        $query = null;
        $_model = new $this->model;
        $tableName = property_exists($_model, 'table') ? ($_model)->table : null;
        $filterable = property_exists($_model, 'filterable') ? ($_model)->filterable : [];
        $_searchable = property_exists($_model, 'searchable') ? ($_model)->searchable : Schema::getColumnListing($_model->getTable());
        $searchable = array_diff($_searchable, $filterable);

        if (isset($params["with"]) && count($params["with"])) {
            $query = call_user_func_array([$this->model, 'with'], [$params['with']]);
        }

        if ($where = Arr::only($params["inputs"], $searchable)) {

            foreach ($where as $param => $value) {

                if ($this->_checkInputValueType($value)) {
                    if (is_array($value) || ($this->isJson($value) && ($value = json_decode($value, true))))
                        $query = call_user_func_array([$query ? $query : $this->model, 'whereIn'], [($tableName ? $tableName . '.' . $param : $param), $value]);
                    else
                        $query = call_user_func_array([$query ? $query : $this->model, 'where'], [($tableName ? $tableName . '.' . $param : $param), 'like', '%' . $value . '%']);
                }
            }
        }
        if ($where = Arr::only($params["inputs"], $filterable)) {
            $query = $this->addWhereToGetAll($query, $where, $tableName);
        }

        if ($first) {
            $query = call_user_func_array([$query, 'first'], []);
        }
        if (is_null($query)) {
            throw new NotFoundHttpException();
        }
        return $query;
    }

    /**
     * @param $value
     * @return bool|int
     */
    public function _checkInputValueType($value)
    {
        return is_string($value) ? strlen($value) : !empty($value);
    }

    /**
     * @param $str
     * @return bool
     */
    public function isJson($str)
    {
        $json = json_decode($str);
        return $json && $str != $json;
    }

    /**
     * @param $query
     * @param $params
     * @param $tableName
     * @param null $model
     * @return mixed
     */
    private function addWhereToGetAll($query, $params, $tableName, $model = null)
    {
        foreach ($params as $param => $value) {

            if ($this->_checkInputValueType($value)) {
                if (is_array($value) || ($this->isJson($value) && ($value = json_decode($value, true))))
                    $query = call_user_func_array([$query ? $query : $model ?? $this->model, 'whereIn'], [($tableName ? $tableName . '.' . $param : $param), $value]);
                else
                    $query = call_user_func_array([$query ? $query : $model ?? $this->model, 'where'], [($tableName ? $tableName . '.' . $param : $param), $value]);
            }
        }
        return $query;
    }

    /**
     * @param $object
     * @return mixed
     */
    public function findOrCreate($object)
    {
        $obj = $this->filter($object)->first();

        if (!$obj) {
            $obj = $this->create(["data" => $object]);
        }


        return $obj;

    }

    /**
     * @param $data
     * @param bool $first
     * @param array $fields
     * @return mixed
     */
    public function filter($data, $first = false, $fields = [])
    {
        return call_user_func_array([call_user_func_array([$this->model, 'where'], [$data]), $first ? 'first' : 'get'], $first || !$fields ? [] : [$fields]);
    }

    /**
     * @array $data
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        $item = call_user_func_array([$this->model, 'create'], [$data["data"]]);
        if (method_exists($this, 'afterCreate')) {
            $this->afterCreate($item, $data);
        }

        return $item;
    }

    /**
     * @param $condition
     * @param $update
     * @param bool $in
     * @return mixed
     */
    public function updateAll($condition, $update, $in = false)
    {
        return call_user_func_array([$this->model, 'where' . ($in ? 'In' : '')], $in ? $condition : [$condition])->update($update);
    }

    /**
     * @param $searchConfig
     * @param array $params
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function search($searchConfig, $params = [])
    {
        $searchKey = $params['inputs']['search_key'] ?? null;
        $searchOn = $params['inputs']['search_on'] ? json_decode($params['inputs']['search_on']) : null;
        $selectFilters = Arr::get($params['inputs'], 'select_filters_mapping', '[]');
        if ($this->isJson($selectFilters)) {
            $selectFilters = json_decode($selectFilters, true);
        } else {
            $selectFilters = [];
        }
        unset($params['inputs']['select_filters_mapping']);
        unset($params['inputs']['search_key']);
        unset($params['inputs']['search_on']);
        $result = [];
        foreach ($searchConfig as $key => $value) {
            if (!isset($searchOn) || array_search($key, $searchOn) > -1) {
                $query = null;
                $this->model = $value['model'];
                $searchableKeys = $value['keys'];
                if ($searchKey && !empty($searchableKeys)) {
                    $query = $this->model::where($searchableKeys[0], 'like', '%' . $searchKey . '%');
                    for ($i = 1; $i < count($searchableKeys); $i++) {
                        $query->orWhere($searchableKeys[$i], 'like', '%' . $searchKey . '%');
                    }
                }
                if (isset($selectFilters[$key])) {
                    $params['inputs']['select_filters'] = json_encode(UtilityService::fromCamelToSnake($selectFilters[$key]));
                }
                $result[$key] = $this->getAll($params, $query);
                unset($params['inputs']['select_filters']);
            }
        }
        return $result;
    }

    /**
     * @param array $params
     * @param null $query
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getAll($params = [], $query = null)
    {
        $_model = new $this->model;
        $tableName = $_model->getTable();
        $filterable = property_exists($_model, 'filterable') ? ($_model)->filterable : [];
        $_searchable = property_exists($_model, 'searchable') ? ($_model)->searchable : Schema::getColumnListing($_model->getTable());
        $searchable = array_diff($_searchable, $filterable);
        $whereNullKeys = property_exists($_model, 'whereNullKeys') ? ($_model)->whereNullKeys : [];


        //use filter from inputs (?user_id=1&model=1389)
        if ($where = Arr::only($params["inputs"], $searchable)) {

            foreach ($where as $param => $value) {


                if ($this->_checkInputValueType($value)) {
                    if (is_array($value) || ($this->isJson($value) && ($value = json_decode($value, true)))) {
                        $query = call_user_func_array([$query ? $query : $this->model, 'whereIn'], [($tableName ? $tableName . '.' . $param : $param), $value]);
                    } else
                        $query = call_user_func_array([$query ? $query : $this->model, 'where'], [($tableName ? $tableName . '.' . $param : $param), 'like', '%' . $value . '%']);
                }
            }
        }

        if ($whereNullKeys = Arr::only($params["inputs"], $whereNullKeys)) {
            $query = $this->addWhereNullToGetAll($query, $whereNullKeys, $tableName);
        }
        if ($where = Arr::only($params["inputs"], $filterable)) {
            $query = $this->addWhereToGetAll($query, $where, $tableName);
        }


        // Added default query on date range if defined in $filterable property
        $dateFilterColumn = isset($params['inputs']['date_filter_column']) ? $params['inputs']['date_filter_column'] : 'created_at';
        if (in_array($dateFilterColumn, $filterable)) {
            // if the date_filter_column is defined as filterable property, then only we search
            if (isset($params['inputs']['from'])) {
                $query = call_user_func_array([$query ?? $this->model, 'where'], [$tableName ? $tableName . '.' . $dateFilterColumn : $dateFilterColumn, '>=', $params['inputs']['from']]);
            }

            if (isset($params['inputs']['to'])) {
                $query = call_user_func_array([$query ?? $this->model, 'where'], [$tableName ? $tableName . '.' . $dateFilterColumn : $dateFilterColumn, '<=', $params['inputs']['to']]);
            }
        }

        //if need paginate must set page (?page=1&perpage=5)
        $page = intval(Arr::get($params['inputs'], 'page'));
        $perpage = Arr::get($params['inputs'], 'perpage');

        //if set with
        if (isset($params["with"]) && count($params["with"])) {
            $query = call_user_func_array([$query ? $query : $this->model, 'with'], [$params['with']]);
        }

        if (in_array('id', $_searchable)) {
            //set order by from input or default ( id , desc )
            $orderby = isset($params["inputs"]["orderby"]) ? $params["inputs"]["orderby"] : ($tableName ? $tableName . '.' . 'id' : 'id');
            $order = isset($params["inputs"]["order"]) ? $params["inputs"]["order"] : "desc";
            $query = call_user_func_array([$query ? $query : $this->model, 'orderby'], [$orderby, $order]);
        }
        $this->applyRelationAndSelectFilters($_model, $query, $params['inputs']);

        //if paginate set use paginate else return all result without paginate
        if ($page < 0) {

            $result = [
                'items' => call_user_func_array([$query ? $query : $this->model, 'get'], [])
            ];
        } else {
            $result = $this->result_for_paginate(call_user_func_array([$query ? $query : $this->model, 'paginate'], [$perpage]));
        }

        return $result;

    }

    /**
     * @param $query
     * @param $keys
     * @param $tableName
     * @param null $model
     * @return mixed
     */
    private function addWhereNullToGetAll($query, $keys, $tableName, $model = null)
    {

        foreach ($keys as $key => $value) {
            $query = call_user_func_array([$query ? $query : $model ?? $this->model, 'whereNull'], [($tableName ? $tableName . '.' . $key : $key)]);
        }
        return $query;
    }

    /**
     * @param $model
     * @param $query
     * @param $inputs
     */
    private function applyRelationAndSelectFilters($model, $query, $inputs)
    {
        $selectFilters = Arr::get($inputs, 'select_filters', '[]');
        if ($this->isJson($selectFilters)) {
            $selectFilters = json_decode($selectFilters, true);
            $selectFilters = UtilityService::fromCamelToSnake($selectFilters);
        } else {
            $selectFilters = [];
        }
        unset($inputs['select_filters']);

        $relationFilters = [];
        if ($this->enableRelationFilters) {
            foreach ($inputs as $key => $value) {
                if (strpos($key, '->') == false) {
                    continue;   // not adding base keys
                }
                $keysArray = explode('->', $key);   // exploding
                $lastRelation = &$relationFilters;          // initially setting lastRelation to blank array as reference
                $lastRelationModel = $model;        // TODO optimize this as common model is still getting validated again
                $keysCount = count($keysArray) - 1;
                foreach ($keysArray as $index => $relation) {
                    if ($keysCount != $index) {
                        if (!method_exists($lastRelationModel, $relation)) {
                            throw new BadRequestHttpException("Invalid relation in query params: $relation");
                            continue;
                        }
                        $lastRelationModel = get_class((new $lastRelationModel)->{$relation}()->getRelated());
                    }
                    if (!($lastRelation[$relation] ?? false)) {
                        $lastRelation[$relation] = [];  // adding relation if not exist
                    }
                    $lastRelation = &$lastRelation[$relation];  // moving pointer ahead to point & add to the last node
                }
                $lastRelation = $value;         // at last setting the last node with the value provided in input
                unset($lastRelation);           // unsetting the variable
            }
            $this->applyRelationFilters($query, $relationFilters, $this->model);
        }

        if ($this->enableSelectFilters) {
            // populating $selectFilters as per relation filters
            $this->populateWhereConditionsForSelectFilters($relationFilters, $selectFilters);

            // applying select filters
            $this->applySelectFilters($query, $selectFilters);
        }
    }

    /**
     * @param $query
     * @param $filters
     * @param $model
     */
    private function applyRelationFilters(&$query, $filters, $model)
    {
        $_model = new $model;
        foreach ($filters as $relationKey => $value) {
            if (is_array($value)) {
                if ($this->selectFilterType == 'whereHas') {
                    $query->{$this->selectFilterType}($relationKey, function ($q) use ($relationKey, $value, $_model) {
                        $relationKeyClass = get_class($_model->{$relationKey}()->getRelated());
                        $this->applyRelationFilters($q, $value, $relationKeyClass);
                    });
                } else {
                    $query->{$this->selectFilterType}([$relationKey => function ($q) use ($relationKey, $value, $_model) {
                        $relationKeyClass = get_class($_model->{$relationKey}()->getRelated());
                        $this->applyRelationFilters($q, $value, $relationKeyClass);
                    }]);
                }
            } else {
                $tableName = $_model->getTable();
                $filterable = property_exists($_model, 'filterable') ? ($_model)->filterable : [];
                $_searchable = property_exists($_model, 'searchable') ? ($_model)->searchable : Schema::getColumnListing($tableName);
                $searchable = array_diff($_searchable, $filterable);
                $whereNullKeys = property_exists($_model, 'whereNullKeys') ? ($_model)->whereNullKeys : [];
                $filter = [$relationKey => $value];
                if ($where = Arr::only($filter, $searchable)) {
                    foreach ($where as $param => $value) {
                        if ($this->_checkInputValueType($value)) {
                            if (is_array($value) || ($this->isJson($value) && ($value = json_decode($value, true)))) {
                                $query = call_user_func_array([$query ? $query : $model, 'whereIn'], [($tableName ? $tableName . '.' . $param : $param), $value]);
                            } else
                                $query = call_user_func_array([$query ? $query : $model, 'where'], [($tableName ? $tableName . '.' . $param : $param), 'like', '%' . $value . '%']);
                        }
                    }
                }

                if ($whereNullKeys = Arr::only($filter, $whereNullKeys)) {
                    $query = $this->addWhereNullToGetAll($query, $whereNullKeys, $tableName, $model);
                }
                if ($where = Arr::only($filter, $filterable)) {
                    $query = $this->addWhereToGetAll($query, $where, $tableName, $model);
                }
            }
        }
    }

    /**
     * @param $relationFilters
     * @param $selectFilters
     */
    protected function populateWhereConditionsForSelectFilters($relationFilters, &$selectFilters)
    {
        foreach ($relationFilters as $relationKey => $relationValue) {
            if (is_array($relationValue)) {
                if (!isset($selectFilters['k'])) {
                    $selectFilters['k'] = [];
                }
                if (!isset($selectFilters['r'])) {
                    $selectFilters['r'] = [];
                }
                if (!isset($selectFilters['c_only'])) {
                    $selectFilters['c_only'] = false;
                }
                if (!isset($selectFilters['r'][$relationKey])) {
                    $selectFilters['r'][$relationKey] = [
                        'k' => [],
                        'r' => null,
                        'c' => null,
                        'c_only' => false
                    ];
                }
                $this->populateWhereConditionsForSelectFilters($relationValue, $selectFilters['r'][$relationKey]);
            } else {
                if (!isset($selectFilters['c'])) {
                    $selectFilters['c'] = [];
                }
                $selectFilters['c'] = array_merge($selectFilters['c'], [$relationKey => $relationValue]);
            }
        }
    }

    /**
     * @param $query
     * @param $filters
     * @param null $relation
     */
    protected function applySelectFilters(&$query, $filters, $relation = null)
    {
        if (is_array($filters['k'] ?? null)) {
            $select = [];
            foreach ($filters['k'] as $k) {
                array_push($select, Str::snake($k));
            }
            if (count($select)) {
                $query->select($select);
            }
        }
        if ($filters['c'] ?? false) {
            $where = [];
            foreach ($filters['c'] as $key => $value) {
                if ($this->isJson($value) && ($value = json_decode($value, true))) {
                    $query->whereIn($key, $value);
                } else {
                    array_push($where, [$key, $value]);
                }
            }
            if (count($where)) {
                $query->where($where);
            }
        }
        if (!is_null($filters['r'] ?? null) && is_array($filters['r'] ?? [])) {
            foreach ($filters['r'] as $relation => $filter) {
                if ($filter['c_only'] ?? false) {
                    $query->withCount($relation);
                } else {
                    $query->with([
                        $relation => function ($q) use ($filter, $relation) {
                            $this->applySelectFilters($q, $filter, $relation);
                        }
                    ]);
                }
            }
        }
    }

    /**
     * @param $collection
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function result_for_paginate($collection)
    {
        return [
            "items" => !empty($this->indexTransformer) ? app()->make($this->indexTransformer)->transformCollection($collection->items()) : $collection->items(),
            "page" => $collection->currentPage(),
            "total" => $collection->total(),
            "pages" => $collection->lastPage(),
            "perpage" => $collection->perPage()
        ];
    }

    /**
     * @param $createConditionalParams
     * @param $updateParams
     * @param bool $withTrashed
     * @return mixed
     * @throws AppException
     */
    public function createOrUpdate($createConditionalParams, $updateParams, $withTrashed = false)
    {
        try {
            DB::beginTransaction();
            if ($withTrashed) {
                $this->model::where($createConditionalParams)->withTrashed()->restore();
            }
            $result = $this->model::updateOrCreate($createConditionalParams, $updateParams);
            DB::commit();
            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new AppException($exception->getMessage());
        }
    }
}
