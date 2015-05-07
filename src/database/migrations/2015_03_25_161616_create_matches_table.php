<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('matches', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->integer('created_by_user_id')->unsigned();
                        $table->foreign('created_by_user_id')->references('id')->on('users');
                        $table->string('name');
                        $table->integer('cardChangeBonusLevel');
			$table->timestamps();
		});
                
                Schema::table('users', function(Blueprint $table)
                {
                        $table->integer('joined_match_id')->nullable()->unsigned();
                        //$table->foreign('joined_match_id')->references('id')->on('matches');
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('matches');
	}

}
