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
                        $table->string('name');
                        $table->string('state');
                        $table->string('roundphase');
                        $table->integer('active_player_id')->unsigned();
                        $table->foreign('active_player_id')->references('id')->on('users');
                        $table->boolean('public');
                        $table->integer('created_by_user_id')->unsigned();
                        $table->foreign('created_by_user_id')->references('id')->on('users');
                        $table->integer('thread_id')->nullable()->unsigned();
                        $table->foreign('thread_id')->references('id')->on('threads');
                        $table->string('joinid');
                        $table->string('mapname');
                        $table->integer('maxusers');
                        $table->integer('cardChangeBonusLevel');
			$table->timestamps();
		});
                
                Schema::table('users', function(Blueprint $table)
                {
                        $table->integer('joined_match_id')->nullable()->unsigned();
                        $table->foreign('joined_match_id')->references('id')->on('matches');
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
                Schema::table('users', function(Blueprint $table)
                {
                        $table->dropForeign('users_joined_match_id_foreign');
                        $table->dropColumn('joined_match_id');
                });
		Schema::drop('matches');
	}

}
