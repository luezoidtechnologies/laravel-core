<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 12/18/17
 * Time: 1:32 PM
 */

namespace Luezoid\Laravelcore\Jobs;


use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BaseJob extends Job
{
    use SerializesModels, InteractsWithQueue;

    public $transformer;
    /**
     * Method
     * @
     * @var Method
     */
    public $transformerMethod;

    /**
     * data
     *
     * @var array
     */
    public $dataToFetch;

    /**
     * Repository
     *
     * @var Repository
     */
    public $repository;

    /**
     * Method
     *
     * @var Method
     */
    public $method;

    /**
     * Event
     *
     * @var Event
     */
    public $event;

    /**
     * data
     *
     * @var array
     */
    public $data;

    /**
     * BaseJob constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return mixed
     */
    public function handle()
    {
        $repository = new $this->repository;
        $item = $repository->{$this->method}($this->data);

        if ($this->event) {
            event(new $this->event($item));
        }

        if ($this->transformer) {
            $transformer = app()->make($this->transformer);
            if (!empty($this->dataToFetch)) {
                $transformer->setPropertiesToFetch($this->dataToFetch);
            }
            return $transformer->{$this->transformerMethod}($item);
        }
        return $item;
    }
}