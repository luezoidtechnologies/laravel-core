<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMinionMissionMappingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('minion_mission_mapping', function(Blueprint $table)
		{
			$table->foreign('minion_id', 'minion_mission_mapping_ibfk_1')->references('id')->on('minions')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('mission_id', 'minion_mission_mapping_ibfk_2')->references('id')->on('missions')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('minion_mission_mapping', function(Blueprint $table)
		{
			$table->dropForeign('minion_mission_mapping_ibfk_1');
			$table->dropForeign('minion_mission_mapping_ibfk_2');
		});
	}

}
