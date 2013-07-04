<?php

use Illuminate\Database\Migrations\Migration;

class CreateCollectionAndTagTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('collections', function($table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('name');
			$table->string('name_unique');
			$table->timestamps();
		});

		Schema::create('collection_upload', function($table)
		{
			$table->integer('collection_id');
			$table->integer('upload_id');
		});

		Schema::create('tags', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->timestamps();
		});

		Schema::create('tag_upload', function($table)
		{
			$table->integer('tag_id');
			$table->integer('upload_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('collections');
		Schema::dropIfExists('collection_upload');
		Schema::dropIfExists('tags');
		Schema::dropIfExists('tag_upload');
	}

}