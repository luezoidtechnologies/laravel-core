<?php


namespace Luezoid\Http\Request;


use Luezoid\Laravelcore\Requests\BaseRequest;
use Luezoid\Laravelcore\Services\RequestService;
use Luezoid\Models\Minion;

class MinionDeleteRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'id' => [
                'required',
                'integer',
                RequestService::exists(Minion::class)
            ]
        ];
    }
}
