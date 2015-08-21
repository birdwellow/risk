<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionNeighborregion extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('region_neighborregion', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->integer('region_id')->nullable()->unsigned();
                        $table->foreign('region_id')->references('id')->on('regions');
                        $table->integer('neighborregion_id')->nullable()->unsigned();
                        $table->foreign('neighborregion_id')->references('id')->on('regions');
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('region_neighborregion');
	}

}
