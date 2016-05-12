<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContintents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('continents', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->string('name');
                        $table->string('colorscheme');
                        $table->integer('troopbonus');
                        $table->integer('match_id')->nullable()->unsigned();
                        $table->foreign('match_id')->references('id')->on('matches');
                        $table->integer('owner_id')->nullable()->unsigned();
                        $table->foreign('owner_id')->references('id')->on('users');
                        $table->nullableTimestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('continents');
	}

}
