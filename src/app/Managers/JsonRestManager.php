<?php namespace Game\Managers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class JsonRestManager {
    
    
    public function getAllUserNamesLikeExcept($pattern, $exceptUsers = array()) {
        
            $exceptIds = array();
            foreach ($exceptUsers as $user){
                array_push($exceptIds, $user->id);
            }
        
            $userNames = array();
            $allUsers = User::where('name', "like", "%".$pattern."%")->get();
            foreach ($allUsers as $user){
                if(!in_array($user->id, $exceptIds)){
                    array_push($userNames, $user->name);
                }
            }
            
            return $userNames;
        
    }
    
}
