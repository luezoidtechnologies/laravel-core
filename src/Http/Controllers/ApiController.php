<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 12/18/17
 * Time: 11:53 AM
 */

namespace Luezoid\Laravelcore\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request as Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Luezoid\Laravelcore\Constants\ErrorConstants;
use Luezoid\Laravelcore\Jobs\BaseJob;
use Luezoid\Laravelcore\Services\EnvironmentService;
use Luezoid\Laravelcore\Services\UtilityService;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;


abstract class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    protected $repo;
    protected $model;
    protected $isCamelToSnake = true;
    protected $isSnakeToCamel = true;

    protected $haveUserCondition = false;

    protected $indexCall = 'getAll';
    protected $indexWith = [];

    protected $showCall = 'show';
    protected $showWith = [];

    protected $storeJobMethod;
    protected $updateJobMethod;
    protected $deleteJobMethod = "delete";
    protected $jobMethod;
    protected $jobEvent;
    protected $jobRepository;
    protected $createJob;
    protected $updateJob;

    protected $deleteJob;
    protected $showRequest;
    protected $storeRequest;
    protected $updateRequest;

    protected $deleteRequest;

    protected $repository;

    protected $request;

    protected $transformer;
    protected $notImplemented = [];
    protected $customRequest;

    protected $customMessage = null;
    protected $defaultMessage = "Resource created/Updated";
    protected $resourceName = "";

    /**
     * Prepare Repository
     * BaseController constructor.
     */
    public function __construct()
    {
        if ($this->repository) {
            $this->repo = new $this->repository;

            if ($this->repo->model) {
                $this->model = ($this->repo)->model;
            }
        }
    }

    /**
     * global index function . return all data of Specific Model.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        $userId = $this->getLoggedInUserId();

        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters()
        );

        if ($this->isCamelToSnake) {
            $inputs = UtilityService::fromCamelToSnake($inputs);
        }

        $result = $this->repo->{$this->indexCall}(["with" => $this->indexWith, "inputs" => $inputs]);

        return $this->standardResponse($result);
    }

    public function getLoggedInUserId()
    {
        return EnvironmentService::getLoggedInUserId();
    }

    public function standardResponse($data, $message = null, $httpCode = 200, $type = null)
    {
        if ($httpCode == 200 && $data && $this->isSnakeToCamel && is_array($data)) {
            $data = UtilityService::fromSnakeToCamel(json_decode(json_encode($data), true));
        }
        return response()->json([
            "message" => $message,
            "data" => $data && method_exists($data, 'toArray') ? $data->toArray() : $data,
            "type" => $type
        ], $httpCode);
    }

    /**
     * global show method  , return selected $id row from Specific Model
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        if ($this->showRequest && $response = $this->validateRequest($this->showRequest)) return $response;

        $result = $this->repo->{$this->showCall}($id, ["with" => $this->showWith]);
        if ($this->transformer) {
            $transformer = app()->make($this->transformer);
            $result = $transformer->transform($result);
        }

        return $this->standardResponse($result);
    }

    protected function validateRequest($method)
    {

        /**
         * create request object
         */

        $this->request = app($method);


        $validator = $this->request->getValidator();


        /**
         * check request is  valid ?
         */
        if ($validator->fails()) {
            return $this->standardResponse(null, $validator->errors()->messages(), 400, ErrorConstants::TYPE_VALIDATION_ERROR);
        }

        return false;

    }

    /**
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->defaultMessage = $this->resourceName ? $this->resourceName . " created successfully" : "Resource Created successfully";
        $this->jobMethod = $this->storeJobMethod ?? 'create';
        if (!$this->createJob) throw new MethodNotAllowedHttpException(['message' => 'Method Not Allowed', 'code' => 405]);

        $data = array_replace_recursive(
            $request->json()->all(),
            $request->route()->parameters()
        );

        if ($this->storeRequest && $response = $this->validateRequest($this->storeRequest)) return $response;

        if ($this->isCamelToSnake) {
            $data = UtilityService::fromCamelToSnake($data);
        }

        $data = $this->requestSanitizer($data, 'createExcept');

        return $this->executeJob($request, $this->createJob, [
            'data' => $data,
        ]);
    }

    private function requestSanitizer($data, $modelKey)
    {
        // removes keys which cannot be updated as defined in model class variable
        $exceptionKeys = [];
        if ($this->model) {
            $_model = new $this->model;
            $exceptionKeys = property_exists($_model, $modelKey) ? ($_model)->{$modelKey} : [];
        }

        if (count((array)$exceptionKeys)) {
            foreach ($exceptionKeys as $key) {
                if (array_key_exists($key, $data)) unset($data[$key]);
            }
        }

        return $data;
    }

    protected function executeJob($request, $jobClass, $params)
    {
        $job = new $jobClass($params);

        if ($jobClass === BaseJob::class) {
            $job->method = $this->jobMethod;
            $job->event = $this->jobEvent;
            $job->repository = $this->jobRepository ? $this->jobRepository : $this->repository;
        }

        return $this->dispatchJob($request, $job, $params);

    }

    protected function dispatchJob($request, $job, $params)
    {
        $result = $this->dispatch($job);
        return $this->standardResponse($result, $this->customMessage ? $this->customMessage : $this->defaultMessage);
    }

    public function __call($method, $arguments)
    {
        parent::__call($method, $arguments);
    }

    /**
     * @param Request $request
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        $this->defaultMessage = $this->resourceName ? $this->resourceName . " updated successfully" : "Resource Updated successfully";
        $this->jobMethod = $this->updateJobMethod ?? 'update';
        if (!$this->updateJob) throw new MethodNotAllowedHttpException(['message' => 'Method Not Allowed', 'code' => 405]);

        $data = array_replace_recursive(
            $request->json()->all(),
            $request->route()->parameters()
        );
        if ($this->updateRequest && $response = $this->validateRequest($this->updateRequest)) return $response;

        if ($this->isCamelToSnake) {
            $data = UtilityService::fromCamelToSnake($data);
        }

        $data = $this->requestSanitizer($data, 'updateExcept');


        return $this->executeJob($request, $this->updateJob, [
            'data' => $data,
            'id' => $id
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws MethodNotAllowedHttpException
     */
    public function destroy(Request $request, $id)
    {
        $this->defaultMessage = $this->resourceName ? $this->resourceName . " deleted successfully" : "Resource deleted successfully";
        $this->jobMethod = $this->deleteJobMethod ?? 'delete';
        if (!$this->deleteJob) throw new MethodNotAllowedHttpException(['message' => 'Method Not Allowed', 'code' => 405]);

        if ($this->deleteRequest && $response = $this->validateRequest($this->deleteRequest)) return $response;

        return $this->executeJob($request, $this->deleteJob, [
            'id' => $id,
            'data' => []
        ]);
    }

    public function getUserByToken()
    {

        $user = Auth::user();
        if (!$user) {
            return $this->standardResponse(null, "Invalid token. No user found.", 401);
        }
        return $user;
    }

    public function getLoggedInUser()
    {
        return EnvironmentService::getLoggedInUser();
    }

    /**
     * @param $job
     * @param null $request
     * @param null $additionalData
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function handleCustomEndPoint($job, $request = null, $additionalData = null)
    {
        if ($this->customRequest && $response = $this->validateRequest($this->customRequest)) return $response;
        $data = [];
        if ($request) {
            $requestData = array_replace_recursive(
                $request->json()->all(),
                $request->route()->parameters()
            );
            $data = UtilityService::fromCamelToSnake($requestData);
        }
        return $this->executeJob($request, $job, [
            'data' => $data,
            'additionalData' => $additionalData
        ]);
    }

    /**
     * @param $job
     * @param null $request
     * @param array $additionalData
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleCustomEndPointGet($job, $request, $additionalData = [])
    {

        if ($this->customRequest && $response = $this->validateRequest($this->customRequest)) return $response;
        $inputs = [];
        if ($request) {
            $inputs = array_replace_recursive(
                $request->all(),
                $request->route()->parameters()
            );
        }

        if ($this->isCamelToSnake) {
            $inputs = UtilityService::fromCamelToSnake($inputs);
        }
        return $this->executeJob($request, $job, ["with" => $this->indexWith, "inputs" => $inputs, "additionalData" => $additionalData]
        );
    }

    public function handleCustomEndPointShow($job, $request, $column, $value)
    {
        if ($this->customRequest && $response = $this->validateRequest($this->customRequest)) return $response;

        $params = [
            'with' => $this->showWith,
            'inputs' => [
                $column => $value
            ]
        ];
        if ($request) {
            $params['inputs'] = array_merge($params['inputs'], $request->all());
        }

        return $this->executeJob($request, $job, $params);
    }

    protected function notImplemented($data, $message = null)
    {
        return $this->standardResponse($data, $message);
    }
}