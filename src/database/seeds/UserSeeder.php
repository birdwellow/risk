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
            'avatarfile' => "Subberbazi_55c23ac1e59ea.jpg"
        ]);
        User::create([
            'name' => "Oberbazi",
            'email' => "ober@bazi.de",
            'password' => Hash::make("pass"),
            'language' => "de",
            'avatarfile' => "Oberbazi_55b7bc6e55cda.gif"
        ]);
        User::create([
            'name' => "SoEinBazi",
            'email' => "soein@bazi.de",
            'password' => Hash::make("pass"),
            'language' => "de",
            'avatarfile' => "SoEinBazi_55b916c7da5f4.gif"
        ]);
        User::create([
            'name' => "ChAoT",
            'email' => "cha@chaos.de",
            'password' => Hash::make("pass"),
            'language' => "de",
            'avatarfile' => "ChAoT_55c49b16b3da8.jpg"
        ]);
        
    }
    
}