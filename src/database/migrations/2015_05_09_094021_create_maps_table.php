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
                        $table->string('templatename');
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
                Schema::table('matches', function(Blueprint $table)
                {
                        $table->dropForeign('matches_map_id_foreign');
                        $table->dropColumn('map_id');
                });
                
		Schema::drop('maps');
	}

}
