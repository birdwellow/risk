<?php namespace Game\Managers;

use Game\User;
use Game\Model\Match;
use Game\Exceptions\GameException;
use \Illuminate\Support\Facades\Session;
use \Illuminate\Support\Facades\Log;
use Game\Model\UUID;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class MatchManager {
    
    
    public function getMatches($criteria = array()){
        
        if(count($criteria) == 0){
            return Match::all();
        }
        
    }
    
    
    public function checkUserCanCreateMatch($user) {
        
            if($user->joinedMatch !== null){
                throw new GameException("USER.ALREADY.JOINED");
            }
        
    }
    
    
    public function checkUserCanDeleteMatch($matchId, $user) {
        
            $match = Match::find($matchId);
            if($match && $user->id !== $match->createdBy->id){
                throw new GameException("USER.CANNOT.DELETE.MATCH");
            }
        
    }
    
    
    public function createMatch($user, $inputs) {

            $invitedPlayersInput = str_replace(" ", "", $inputs['invited_players']);
            $invitedPlayerNames = explode(",", $invitedPlayersInput);
            $invitedPlayers = array();
            foreach ($invitedPlayerNames as $invitedPlayerName){
                $foundPlayer = User::where("name", $invitedPlayerName)->first();
                if($foundPlayer){
                    array_push($invitedPlayers, $foundPlayer);
                }
            }

            $match = new Match();
            $match->name = $inputs['name'];
            $match->createdBy()->associate($user);
            $match->save();
            
            $user->joinedMatch()->associate($match);
            $user->save();
            return $match;
            
    }
    
    public function deleteMatch($id) {
        
            $match = Match::find($id);
            if(!$match){
                throw new GameException("MATCH.NOT.FOUND");
            } else {
                $match->delete();
            }
        
    }
    
    public function goToMatch($id, $user) {
        
            if($user->joinedMatch && $user->joinedMatch->id !== $id){
                throw new GameException("USER.ALREADY.JOINED.ANOTHER.MATCH");
            }
            $match = Match::find($id);
            if($match == null){
                throw new GameException("MATCH.NOT.FOUND");
            } else {
                $user->joinid = UUID::v5("1546058f-5a25-4334-85ae-e68f2a44bbaf", $user->name);
                $user->joinedMatch()->associate($match);
                $user->save();
                
                return $match;
            }
        
    }
    
}
