<?php namespace Game\Managers;

use Game\Model\Match;
use Game\Exceptions\GameException;
use Game\Services\IdTokenService;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Game\User;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class MatchManager {
    
    
        const MATCHSTATE_WAITING_FOR_PLAYERJOINS = "waitingforjoins";
        const MATCHSTATE_STARTED = "started";
        const MATCHSTATE_FINISHED = "finished";
        
        const ROUNDPHASE_TROOPGAIN = "troopgain";
        const ROUNDPHASE_TROOPDEPLOYMENT = "troopdeployment";
        const ROUNDPHASE_ATTACK = "attack";
        const ROUNDPHASE_TROOPSHIFT = "troopshift";
        
        static protected $COLORSCHEMES = [
            "red",
            "blue",
            "green", 
            "yellow",
            "orange",
            "brown",
            "purple",
        ];
        
        protected $mapTemplates = [
            "earth",
        ];


        public function __construct() {

        }


        public function checkUserMayCreateMatch($user) {

                if($user->joinedMatch !== null){
                    throw new GameException("ALREADY.JOINED");
                }

        }


        public function checkUserCanDeleteMatch($user, $match) {

                if($user->id !== $match->createdBy->id){
                    throw new GameException("CANNOT.DELETE.MATCH");
                }

        }


        public function checkUserCanAdministrateMatch($user, $match) {

                if($user->id !== $match->createdBy->id){
                    throw new GameException("CANNOT.DELETE.MATCH");
                }

        }

        
        public function checkUserMayJoinMatch($user, $match) {

                if($user->joinedMatch !== null){
                    throw new GameException("ALREADY.JOINED");
                }
                if($match->state !== self::MATCHSTATE_WAITING_FOR_PLAYERJOINS){
                    throw new GameException("MATCH.CLOSED");
                }
                
                if(!$match->public){
                    $isParticipant = false;
                    foreach($match->thread->participants as $participant){
                        if($user->id == $participant->user->id){
                            $isParticipant = true;
                        }
                    }
                    if(!$isParticipant){
                        throw new GameException("ONLY.INVITED.USERS");
                    }
                }

        }


        public function getAllMatches(){

            return Match::all();

        }


        public function getAllPublicMatches(){

            return Match::where("public", true)->get();

        }


        public function getAllPublicWaitingMatches(){

            return Match
                ::where("public", true)
                ->where("state", self::MATCHSTATE_WAITING_FOR_PLAYERJOINS)
                ->get();

        }
        
        
        public function getMapNames() {

            return $this->mapTemplates;

        }


        public function getMatchForId($id){

            $match = Match::find($id);
            if(!$match) {
                throw new GameException("MATCH.NOT.FOUND");
            }
            return $match;

        }


        public function getMatchForJoinId($joinId){

            $match = Match::where("joinid", $joinId)->first();
            
            if(!$match) {
                throw new GameException("MATCH.NOT.FOUND");
            }
            return $match;


        }


        public function getMatchForUser($user){

            $match = $user->joinedMatch;
            if(!$match) {
                throw new GameException("MATCH.NOT.FOUND");
            }
            return $match;


        }
        
        
        public function getUntakenColorSchemesForMatch($match) {
            
            $takenColorSchemes = array();
            
            $users = $match->joinedUsers;
            foreach($users as $user){
                array_push($takenColorSchemes, $user->matchcolor);
            }
            
            $allowedColorSchemes = array_diff(self::$COLORSCHEMES, $takenColorSchemes);
            
            return $allowedColorSchemes;
            
        }


        public function createMatch($name, $mapName, $maxUsers, $creatorUser, $thread, $isPublic) {

                $match = new Match();
                $match->name = $name;
                $match->createdBy()->associate($creatorUser);
                $match->maxusers = $maxUsers;
                $match->public = $isPublic;
                $token = IdTokenService::createToken();
                while(Match::where("joinid", $token)->first()){
                    $token = IdTokenService::createToken();
                }
                $match->mapname = $mapName;
                $match->joinid = $token;
                $match->state = self::MATCHSTATE_WAITING_FOR_PLAYERJOINS;
                $match->thread()->associate($thread);
                
                $match->save();
                
                $creatorUser->matchescreated += 1;
                $creatorUser->save();

                return $match;

        }

        
        public function joinUserToMatch($match, $user, $colorScheme) {

                if($match->state !== self::MATCHSTATE_WAITING_FOR_PLAYERJOINS){
                    throw new GameException("MATCH.CLOSED");
                }
                $user->joinedMatch()->associate($match);
                $user->save();
                $match->load("joinedUsers");
                
                $user->matchcolor = $colorScheme;
                $user->matchnotification = null;
                $user->save();
                
                if(count($match->joinedUsers) >= $match->maxusers){
                    $this->startMatch($match);
                }


        }

        
        public function cancelMatch(Match $match) {

                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                foreach ($match->continents as $continent) {
                    foreach ($continent->regions as $region) {
                        foreach ($region->neighbors as $neighbor) {
                            $region->neighbors()->detach($neighbor);
                            $neighbor->neighbors()->detach($region);
                        }
                        $region->delete();
                    }
                    $continent->delete();
                }
                foreach ($match->connections as $connection) {
                    $connection->delete();
                }
                foreach ($match->joinedUsers as $joinedUser) {
                    $result = $joinedUser->joinedMatch()->dissociate();
                    $joinedUser->matchorder = 0;
                    $joinedUser->matchnotification = "match:cancelled";
                    $joinedUser->save();
                }

                $match->delete();
                
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        }

        
        public function startMatch($match){
            
                $joinedUsers = $match->joinedUsers;
                if(count($joinedUsers) < 2 || count($joinedUsers) > 6){
                    throw new GameException("CANNOT.START.MATCH");
                }
                $shuffledJoinedUsers = $joinedUsers->shuffle();
                
                $i = 1;
                foreach($shuffledJoinedUsers as $randomJoinedUser){
                    $randomJoinedUser->matchesplayed += 1;
                    $randomJoinedUser->matchorder = $i;
                    $randomJoinedUser->save();
                    $i++;
                }
                
                $this->distrubuteRegionsRandomlyToJoinedUsers($match);
                
                $this->distrubuteDeployTroopsRandomlyToRegions($match);
                
                $firstPlayer = $shuffledJoinedUsers->first();
                $match->activePlayer()->associate($firstPlayer);
                $firstPlayer->matchnotfication = "match:yourturn";
                $match->state = self::MATCHSTATE_STARTED;
                $match->roundphase = self::ROUNDPHASE_TROOPGAIN;
                
                $newTroopsObject = $this->getNewTroopsObjectForUser($firstPlayer);
                foreach ($newTroopsObject as $newTroops) {
                    $firstPlayer->newtroops += $newTroops;
                }
                $match->roundphasedata = json_encode($newTroopsObject);
                
                $match->save();
                $firstPlayer->save();

        }


    
        public function getNewTroopsObjectForUser(User $player) {

            $newTroopsObject = new \stdClass();

            $newTroopsObject->base = 3;

            $regions = $player->regions;
            $newTroopsObject->regions = floor(count($regions)/3);

            foreach ($player->continents as $continent){
                $continentName = $continent->name;
                $newTroopsObject->$continentName = $continent->troopbonus;
            }

            return $newTroopsObject;

        }


        public function changeMatchData($match, $maxUsers, $isPublic) {
            
            $match->maxusers = (int) $maxUsers;
            $match->public = (boolean) $isPublic;
            $match->save();
            
            if(count($match->joinedUsers) >= $match->maxusers){
                $this->startMatch($match);
            }

        }
        
        
        
        protected function distrubuteRegionsRandomlyToJoinedUsers(Match $match) {
            
                $joinedUsers = $match->joinedUsers;
                $continents = $match->continents;
                $regions = new Collection();
                foreach ($continents as $continent){
                    $continentRegions = $continent->regions;
                    foreach ($continentRegions as $region){
                        $regions->push($region);
                    }
                }
                $regions->shuffle();
                
                $joinedUsersArray = $joinedUsers->all();
                $index = 0;
                foreach ($regions as $region){
                    
                    if($index >= count($joinedUsersArray) || !isset($joinedUsersArray[$index])){
                        $index = 0;
                    }
                    
                    $user = $joinedUsersArray[$index];
                    
                    $region->owner()->associate($user);
                    $region->save();
                    
                    $index++;
                    
                }
        }
        
        
        protected function distrubuteDeployTroopsRandomlyToRegions(Match $match) {
            
            $userCount = count($match->joinedUsers);
            $troopsPerUser = $this->getTroopsForUserNumber($userCount);
            
            foreach ($match->joinedUsers as $player) {
                $troops = $troopsPerUser;
                foreach ($player->regions as $playerRegion) {
                    $playerRegion->troops++;
                    $playerRegion->save();
                    $troops--;
                }
                
                while ($troops > 0){
                    $randomPlayerRegion = $player->regions->random();
                    $randomPlayerRegion->troops++;
                    $randomPlayerRegion->save();
                    $troops--;
                }
            }
            
        }
        
        
        protected function getTroopsForUserNumber($userNumber){
            
            switch($userNumber){
                case 2 : return 40;
                case 3 : return 35;
                case 4 : return 30;
                case 5 : return 25;
                case 6 : return 20;
            }
            
        }
    
}
