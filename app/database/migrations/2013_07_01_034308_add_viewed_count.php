<?php

use Illuminate\Database\Migrations\Migration;

class AddViewedCount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('uploads', function($table)
		{
			$table->integer('viewed')->default(0)->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('uploads', function($table)
		{
			$table->dropColumn('viewed');
		});
	}

}