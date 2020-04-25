<?php

namespace Luezoid\Http\Requests;

use Luezoid\Models\Minion;
use Luezoid\Models\Mission;
use Luezoid\Laravelcore\Requests\BaseRequest;
use Luezoid\Laravelcore\Services\RequestService;

class MissionCreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required',
                'string',
                'min:5',
                'max:255',
                RequestService::unique(Mission::class, 'name')  // Mission name has to be unique
            ],
            'description' => 'present|nullable|min:10|max:1000',
            'minionId' => [
                'required',
                'integer',
                RequestService::exists(Minion::class)   // Minion must exists
            ]
        ];
    }
}
