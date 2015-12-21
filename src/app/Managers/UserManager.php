<?php namespace Game\Managers;

use Game\User;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class UserManager {


        public function getAllUserNamesLikeExcept($pattern, $exceptUserNames = array()) {

                $allUsers = User::where('name', "like", "%".$pattern."%")->get();
                return $this->extractUserNamesFromUsers($allUsers, $exceptUserNames);

        }


        public function getAllUserNames($exceptUserNames = array()) {

                $allUsers = User::all();
                return $this->extractUserNamesFromUsers($allUsers, $exceptUserNames);
        }


        public function getAllUsers($exceptUserName) {
                
                if($exceptUserName){
                    return $allUsers = User::where('name', "!=", $exceptUserName)->get();
                } else {
                    return $allUsers = User::all();
                }
        }


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


        public function getUserForId($id) {

                $user = User::find($id);
                
                if(!$user){
                    throw new \Game\Exceptions\GameException("USER.NOT.FOUND");
                }

                return $user;

        }


        public function extractUserIdsFromUsers($userArray){

                $userIds = array();

                foreach ($userArray as $user) {
                    array_push($userIds, $user->id);
                }

                return $userIds;

        }
    
    
        private function extractUserNamesFromUsers($userArray, $filterNames = array()){

                $userNames = array();
                foreach ($userArray as $user){
                    if(!in_array($user->name, $filterNames)){
                        array_push($userNames, $user->name);
                    }
                }
                return $userNames;

        }

    
}
