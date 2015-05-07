<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserJoinMatchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_join_match', function(Blueprint $table)
		{
			$table->increments('id');
                        
                        $table->integer('user_id')->unsigned();
                        $table->foreign('user_id')->references('id')->on('users');
                        
                        $table->integer('match_id')->unsigned();
                        $table->foreign('match_id')->references('id')->on('matches');
                        
                        $table->integer('invited_by_user_id')->unsigned();
                        $table->foreign('invited_by_user_id')->references('id')->on('users');
                        
                        $table->string('basecolor');
                        
                        $table->string('status');
                        
                        $table->string('invitation_message');
			
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
		Schema::drop('user_join_match');
	}

}
