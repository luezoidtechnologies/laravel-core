<?php

/**
 * Created by Reliese Model.
 */

namespace Luezoid\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Minion
 *
 * @property int $id
 * @property string $name
 * @property int $total_eyes
 * @property string $favourite_sound
 * @property bool $has_hairs
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package Luezoid\Models
 */
class Minion extends Model
{
    protected $table = 'minions';

    protected $casts = [
        'total_eyes' => 'int',
        'has_hairs' => 'bool'
    ];

    protected $fillable = [
        'name',
        'total_eyes',
        'favourite_sound',
        'has_hairs'
    ];

    public $filterable = [
        'total_eyes',
        'has_hairs'
    ];

    public $createExcept = [
        'id'
    ];

    public $updateExcept = [
        'total_eyes',
        'has_hairs'
    ];
}
