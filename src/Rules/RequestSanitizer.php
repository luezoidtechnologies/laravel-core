<?php
/**
 * Created by PhpStorm.
 * User: choxx
 * Date: 10/07/19
 * Time: 12:58 PM
 */

namespace Luezoid\Laravelcore\Rules;

use Illuminate\Contracts\Validation\Rule;

class RequestSanitizer implements Rule
{
    private $data;
    private $message = '';
    private $conditions;
    private $and;


    /**
     * DataSanitizer constructor.
     * @param array $data
     * @param array $conditions
     * @param bool $and
     */
    public function __construct(array $data, array $conditions, $and = true)
    {
        $this->data = $data;
        $this->conditions = $conditions;
        $this->and = $and;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->message = "$attribute is not required.";
        if (isset($value)) {
            $isAllowedArray = [];

            foreach ($this->conditions as $index => $condition) {
                $isAllowedArray[$index] = false;
                if ($condition['type'] == '!value') {
                    if (isset($this->data[$condition['keyName']]) && ($this->data[$condition['keyName']] != $condition['value'])) {
                        $isAllowedArray[$index] = true;
                    }
                } elseif ($condition['type'] == 'value') {
                    if (isset($this->data[$condition['keyName']]) && ($this->data[$condition['keyName']] == $condition['value'] || (is_array($condition['value']) && in_array($this->data[$condition['keyName']], $condition['value'])))) {
                        $isAllowedArray[$index] = true;
                    }
                } else if ($condition['type'] == 'present') {
                    if (is_array($condition['keyName'])) {

                        foreach ($condition['keyName'] as $val) {
                            if (isset($this->data[$val]) && !empty($this->data[$val])) {
                                $isAllowedArray[$index] = true;
                                break;
                            }
                        }
                    } else {
                        if (isset($this->data[$condition['keyName']]) && !empty($this->data[$condition['keyName']])) {
                            $isAllowedArray[$index] = true;
                        }
                    }
                }
            }
            if ($this->and) {
                $isAllowed = null;
                foreach ($isAllowedArray as $value) {
                    if (is_null($isAllowed)) {
                        $isAllowed = $value;
                    } else {
                        $isAllowed = $isAllowed && $value;
                    }
                }
            } else {
                $isAllowed = false;
                foreach ($isAllowedArray as $value) {
                    if ($value == true) {
                        $isAllowed = true;
                        break;
                    }
                }
            }
            return $isAllowed;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
