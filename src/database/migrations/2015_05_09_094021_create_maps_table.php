<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('maps', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->string('name');
                        $table->timestamps();
		});
                
                Schema::table('matches', function(Blueprint $table)
                {
                        $table->integer('map_id')->nullable()->unsigned();
                        $table->foreign('map_id')->references('id')->on('maps');
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
                DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::drop('maps');
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}

}
