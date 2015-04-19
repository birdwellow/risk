<?php

use Game\User;

class MatchManagerTest extends TestCase {
    
        public function setUp() {
                
                parent::setUp();
            
                $user = new User(["name" => "TestUser"]);
                $this->be($user);
                
        }

	/**
	 * 
	 *
	 * @return void
	 */
	public function testOverviewCanBeCalled()
	{       
                $response = $this->call('GET', '/home');

		$this->assertEquals(200, $response->getStatusCode());
	}

}
