<?php

use Illuminate\Database\Migrations\Migration;

class InitialSetup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table){
			$table->increments('id');
			$table->string('username', 64);
			$table->string('email');
			$table->string('password', 60);
			$table->timestamps();
		});

		Schema::create('uploads', function($table){
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('filegroup_id')->default(0);
			$table->string('description', 255);
			$table->string('name', 255);
			$table->string('cleanname', 255);
			$table->string('originalname', 255);
			$table->string('ext', 10);
			$table->integer('size')->unsigned();
			$table->string('type', 255);
			$table->string('extra', 32);
			$table->timestamps();
		});
		
		Schema::create('images', function($table){
			$table->increments('id');
			$table->integer('upload_id');
			$table->string('type', 255);
			$table->integer('width');
			$table->integer('height');
			$table->integer('bits');
			$table->integer('channels');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('images');
		Schema::drop('uploads');
		Schema::drop('users');
	}

}