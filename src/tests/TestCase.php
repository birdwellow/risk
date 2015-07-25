<?php

use Illuminate\Support\Facades\Artisan;
use Game\User;
use Game\Model\Match;
use Game\Model\Invitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

        protected static $keepTables = array(
            "migrations",
        );
    
	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$app = require __DIR__.'/../bootstrap/app.php';

		$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

		return $app;
	}
    
        
        public function setUp() {
                
                parent::setUp();
                $this->clearAndReseedDb();
                
        }
        
        
        public function tearDown() {
            
                //parent::tearDown();
            
        }


        static function setUpBeforeClass() {
            
                parent::setUpBeforeClass();
                
        }

        
        static function tearDownAfterClass() {
            
                //self::resetDb();
                parent::tearDownAfterClass();
            
        }
        
        
        protected static function resetDb(){
            
                Artisan::call("migrate:reset");
                Artisan::call("migrate");
                Artisan::call("db:seed");
            
        }
        
        
        protected static function clearAndReseedDb(){
            
                DB::statement("SET foreign_key_checks=0");
                
                foreach (DB::select('SHOW TABLES') as $table) {
                    $tableNames[] = array_values((array)$table)[0];
                }
                foreach($tableNames as $tableName) {
                    if(!in_array($tableName, self::$keepTables)){
                        DB::table($tableName)->truncate();
                    }
                }
                
                DB::statement("SET foreign_key_checks=1");
                
                Artisan::call("db:seed");
            
        }

}
