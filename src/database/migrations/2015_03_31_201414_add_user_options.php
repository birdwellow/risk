<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserOptions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                Schema::table('users', function(Blueprint $table)
                {
                        $table->string('language');
                        $table->string('colorscheme');
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
                        $table->dropColumn('language');
                        $table->dropColumn('colorscheme');
                });
	}

}
