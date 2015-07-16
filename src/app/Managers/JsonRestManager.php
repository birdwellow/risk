<?php namespace Game\Managers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Game\User;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class JsonRestManager {
    
    
    private function extractNames($userArray, $filterNames = array()){
        
            $userNames = array();
            foreach ($userArray as $user){
                if(!in_array($user->name, $filterNames)){
                    array_push($userNames, $user->name);
                }
            }
            return $userNames;
        
    }
    
    
    public function getAllUserNamesLikeExcept($pattern, $exceptUserNames = array()) {
        
            $allUsers = User::where('name', "like", "%".$pattern."%")->get();
            return $this->extractNames($allUsers, $exceptUserNames);
        
    }
    
    
    public function getAllUserNames($exceptUserNames = array()) {
        
            $allUsers = User::all();
            return $this->extractNames($allUsers, $exceptUserNames);
    }
    
}
