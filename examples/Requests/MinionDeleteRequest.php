<?php


namespace App\Http\Request;


use App\Models\Minion;
use Luezoid\Laravelcore\Requests\BaseRequest;
use Luezoid\Laravelcore\Services\RequestService;

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
