<?php

/**
 * Created by Reliese Model.
 */

namespace Luezoid\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mission
 * 
 * @property int $id
 * @property string $name
 * @property int $lead_by_id
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Minion $lead_by
 * @property Collection|Minion[] $minions
 *
 * @package App\Models
 */
class Mission extends Model
{
	protected $table = 'missions';

	protected $casts = [
		'lead_by_id' => 'int'
	];

	protected $fillable = [
		'name',
		'lead_by_id',
		'description'
	];

	public function lead_by()
	{
		return $this->belongsTo(Minion::class, 'lead_by_id');
	}

	public function minions()
	{
		return $this->belongsToMany(Minion::class, 'minion_mission_mapping')
					->withPivot('id')
					->withTimestamps();
	}
}
