<?php


namespace App\Http\Request;


use Luezoid\Laravelcore\Requests\BaseRequest;

class MinionCreateRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'totalEyes' => 'required|integer|min:0|max:2',
            'favouriteSound' => 'present|nullable|string|max:255',
            'hasHairs' => 'required|boolean'
        ];
    }
}
