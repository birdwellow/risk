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
    
    
    public function getAllUserNamesLikeExcept($pattern, $exceptUserNames = array()) {
        
            $userNames = array();
            $allUsers = User::where('name', "like", "%".$pattern."%")->get();
            foreach ($allUsers as $user){
                if(!in_array($user->name, $exceptUserNames)){
                    array_push($userNames, $user->name);
                }
            }
            
            return $userNames;
        
    }
    
}
