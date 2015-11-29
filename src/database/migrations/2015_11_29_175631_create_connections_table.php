<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConnectionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('connections', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->json('points');
                        $table->integer('match_id')->nullable()->unsigned();
                        $table->foreign('match_id')->references('id')->on('matches');
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
		Schema::drop('connections');
	}

}
