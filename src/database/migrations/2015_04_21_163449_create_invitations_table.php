<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invitations', function(Blueprint $table)
		{
			$table->increments('id');
                        
                        $table->integer('user_id')->unsigned();
                        $table->foreign('user_id')->references('id')->on('users');
                        
                        $table->integer('match_id')->unsigned();
                        $table->foreign('match_id')->references('id')->on('matches');
                        
                        $table->integer('invited_by_user_id')->unsigned();
                        $table->foreign('invited_by_user_id')->references('id')->on('users');
                        
                        $table->string('status');
                        
                        $table->string('message');
			
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
		Schema::drop('invitations');
	}

}
