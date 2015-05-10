<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Game\Model\Map;

class MapSeeder extends Seeder {
    
    public function run() {
        
        DB::table('maps')->delete();
        
        Map::create(['name' => 'Earth']);
        Map::create(['name' => 'Middle-Earth']);
        Map::create(['name' => 'After-Apocalypse']);
        
    }
    
}
