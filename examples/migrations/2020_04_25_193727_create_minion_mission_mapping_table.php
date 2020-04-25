<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMinionMissionMappingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('minion_mission_mapping', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('minion_id')->unsigned()->index('minion_id');
			$table->integer('mission_id')->unsigned()->index('mission_id');
			$table->timestamps();
			$table->unique(['minion_id','mission_id'], 'minion_mission_unique');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('minion_mission_mapping');
	}

}
