<?php

/**
 * Created by Reliese Model.
 */

namespace Luezoid\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MinionMissionMapping
 *
 * @property int $id
 * @property int $minion_id
 * @property int $mission_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Minion $minion
 * @property Mission $mission
 *
 * @package Luezoid\Models
 */
class MinionMissionMapping extends Model
{
    protected $table = 'minion_mission_mapping';

    protected $casts = [
        'minion_id' => 'int',
        'mission_id' => 'int'
    ];

    protected $fillable = [
        'minion_id',
        'mission_id'
    ];

    public function minion()
    {
        return $this->belongsTo(Minion::class);
    }

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
}
