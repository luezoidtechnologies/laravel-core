<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('missions', function(Blueprint $table)
		{
			$table->foreign('lead_by_id', 'missions_ibfk_1')->references('id')->on('minions')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('missions', function(Blueprint $table)
		{
			$table->dropForeign('missions_ibfk_1');
		});
	}

}
