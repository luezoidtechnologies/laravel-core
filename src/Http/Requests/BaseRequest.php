<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 12/18/17
 * Time: 2:23 PM
 */

namespace Luezoid\Laravelcore\Requests;


use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    public $rules;
    protected $inputs = [];

    public function authorize()
    {
        return true;
    }

    abstract function rules();

    /**
     * The data to be validated should be processed as JSON.
     * @return mixed
     */
    protected function validationData()
    {
        $inputs = array_replace_recursive(
            $this->json()->all(),
            $this->all(),
            $this->route()->parameters()
        );
        $this->inputs = array_merge($this->inputs, $inputs);
        return $this->inputs;
    }

    /**
     * Add extra variable(s) in the input request data. Can be used in any child Request Class.
     * @param $array
     */
    protected function add($array)
    {
        $this->inputs = array_merge($this->inputs, $array);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return mixed
     */
    public function getValidator()
    {
        return $this->getValidatorInstance();
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $this->validator = $validator;
    }

    public function getEnvironmentId()
    {
        return $this->header('env_id', null);
    }
}