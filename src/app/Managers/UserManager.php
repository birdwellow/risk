<?php namespace Game\Managers;

use Game\User;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class UserManager {
    
    
    public function findUsersForNames($userNameArray) {
        
        $users = array();
        
        foreach ($userNameArray as $userName) {
            $foundUser = User::where("name", trim($userName))->first();
            if ($foundUser) {
                array_push($users, $foundUser);
            }
        }
        
        return $users;
        
    }
    
    
    public function extractUserIdsFromUsers($userArray){
        
        $userIds = array();
        
        foreach ($userArray as $user) {
            array_push($userIds, $user->id);
        }
        
        return $userIds;
    }
    
    
}
