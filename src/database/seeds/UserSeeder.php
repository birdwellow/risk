<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Game\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    
    public function run() {
        
        DB::table('users')->delete();
        
        User::create([
            'name' => "Subberbazi",
            'email' => "subber@bazi.de",
            'password' => Hash::make("pass"),
            'language' => "en",
            'avatarfile' => "Subberbazi_5527efc91bdaf.jpg"
        ]);
        User::create([
            'name' => "Oberbazi",
            'email' => "ober@bazi.de",
            'password' => Hash::make("pass"),
            'language' => "de"
        ]);
        User::create([
            'name' => "SoEinBazi",
            'email' => "soein@bazi.de",
            'password' => Hash::make("pass"),
            'language' => "de"
        ]);
        User::create([
            'name' => "ChAoT",
            'email' => "cha@chaos.de",
            'password' => Hash::make("pass"),
            'language' => "de"
        ]);
        
    }
    
}