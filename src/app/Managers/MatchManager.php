<?php namespace Game\Managers;

use Game\User;
use Game\Model\Match;
use Game\Exceptions\GameException;
use \Illuminate\Support\Facades\Session;
use \Illuminate\Support\Facades\Log;
use Game\Model\UUID;
use Game\Model\UserJoinMatch;
use Game\Model\Map;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class MatchManager {
    
    
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
    
    
    public function checkUserCanAdministrateMatch($match, $user) {
        
            if($match && $user->id !== $match->createdBy->id){
                throw new GameException("USER.CANNOT.DELETE.MATCH");
            }
        
    }
    
    
    public function getMatches($criteria = array()){
        
        if(count($criteria) == 0){
            return Match::all();
        }
        
    }
    
    
    public function createMatch($user, $inputs) {

            $match = new Match();
            $match->name = $inputs['name'];
            $match->createdBy()->associate($user);
            $match->closed = ($inputs["closed"] !== null);
            Log::info("Maxusers: " .$inputs["maxusers"]);
            $match->maxusers = $inputs["maxusers"];
            $map = Map::where("name", $inputs['mapName'])->first();
            $match->map()->associate($map);
            
            $match->save();
            

            $invitedPlayersInput = str_replace(", ", ",", $inputs['invited_players']);
            $invitedPlayerNames = explode(",", $invitedPlayersInput);
            $invitationMessage = $inputs['invitation_message'];
            foreach ($invitedPlayerNames as $invitedPlayerName){
                $foundPlayer = User::where("name", $invitedPlayerName)->first();
                if($foundPlayer){
                    $userJoinMatch = new UserJoinMatch();
                    $userJoinMatch->status = "invited";
                    $userJoinMatch->invitedBy()->associate($user);
                    $userJoinMatch->user()->associate($foundPlayer);
                    $userJoinMatch->match()->associate($match);
                    $userJoinMatch->invitation_message = $invitationMessage;
                    $userJoinMatch->save();
                }
            }
            
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
    
    public function rejectInvitation($id, $user) {
        
        $join = UserJoinMatch::find($id)->first();
        Log::info($join);
        
        if($user->id == $join->user->id){
            
            $join->status = "rejected";
            $join->save();
            
        }
        else {
            
            // throw Exception?
            
        }
        
    }
    
    public function deleteInvitation($id, $user) {
        
        $join = UserJoinMatch::find($id)->first();
        
        if($user->id == $join->invitedBy->id){
            
            $join->delete();
            
        }
        else {
            
            // throw Exception?
            
        }
        
    }
    
    
    public function saveAdministratedMatch($match, $user, $inputs) {
        
            $invitedPlayersInput = str_replace(", ", ",", $inputs['invited_players']);
            $invitedPlayerNames = explode(",", $invitedPlayersInput);
            $invitationMessage = $inputs['invitation_message'];
            foreach ($invitedPlayerNames as $invitedPlayerName){
                $foundPlayer = User::where("name", $invitedPlayerName)->first();
                if($foundPlayer){
                    $userJoinMatch = new UserJoinMatch();
                    $userJoinMatch->status = "invited";
                    $userJoinMatch->invitedBy()->associate($user);
                    $userJoinMatch->user()->associate($foundPlayer);
                    $userJoinMatch->match()->associate($match);
                    $userJoinMatch->invitation_message = $invitationMessage;
                    $userJoinMatch->save();
                }
            }
        
    }
        
    public function getMapNames() {

        $maps = Map::all();
        $mapNames = array();
        foreach ($maps as $map) {
            $mapNames[] = $map->name;
        }
        return $mapNames;

    }
    
}
