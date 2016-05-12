<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('email')->unique();
			$table->string('password', 60);
			$table->string('role')->default('user');
			$table->rememberToken();
			$table->nullableTimestamps();
                        $table->string('joinid')->nullable();
                        $table->string('avatarfile')->nullable();
                        $table->string('language')->nullable();
                        $table->string('matchcolor')->nullable();
                        $table->integer('matchorder')->nullable();
                        $table->boolean('isonline')->default(false);
                        $table->string('newtroops')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
