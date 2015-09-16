<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('regions', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->string('name');
                        
                        $table->integer('troops');
                        
                        $table->integer('continent_id')->nullable()->unsigned();
                        $table->foreign('continent_id')->references('id')->on('continents');
                        $table->integer('owner_id')->nullable()->unsigned();
                        $table->foreign('owner_id')->references('id')->on('users');
                        
                        $table->string('cardunittype');
                        $table->integer('card_owner_id')->nullable()->unsigned();
                        $table->foreign('card_owner_id')->references('id')->on('users');
                        
                        $table->longText('svgdata');
                        $table->integer('centerx');
                        $table->integer('centery');
                        $table->integer('labelcenterx');
                        $table->integer('labelcentery');
                        $table->integer('angle');
                        $table->string('pathdata');
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
		Schema::drop('regions');
	}

}
