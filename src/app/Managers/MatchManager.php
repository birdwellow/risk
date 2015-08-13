<?php namespace Game\Managers;

use Game\User;
use Game\Model\Match;
use Game\Exceptions\GameException;
use Game\Services\IdTokenService;
use Game\Model\Invitation;
use Game\Model\Map;

use Illuminate\Support\Facades\Log;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class MatchManager {
    
    
    const MATCHSTATE_WAITING_FOR_PLAYERJOINS = "match.waitingforjoins";
    const MATCHSTATE_START = "match.started";


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
    
    public function checkUserMayJoinMatch($user) {
        
            if($user && $user->joinedMatch !== null){
                throw new GameException("ALREADY.JOINED");
            }
        
    }
    
    
    public function getAllMatches(){
        
        return Match::all();
        
    }
    
    
    public function createMatch($name, $mapName, $maxUsers, $creatorUser) {

            $match = new Match();
            $match->name = $name;
            $match->createdBy()->associate($creatorUser);
            $match->maxusers = $maxUsers;
            $token = IdTokenService::createToken();
            while(Match::where("joinid", $token)->first()){
                $token = IdTokenService::createToken();
            }
            $match->joinid = $token;
            $match->state = self::MATCHSTATE_WAITING_FOR_PLAYERJOINS;
            $map = Map::where("name", $mapName)->first();
            $match->map()->associate($map);
            
            $match->save();
            
            return $match;
            
    }
    
    public function cancelMatch($match) {
        
            foreach ($match->joinedUsers() as $joinedUser) {
                $joinedUser->joinedMatch()->detach();
            }

            $match->delete();
            
    }
    
    public function joinUserToMatch($match, $user) {
        
            if($match->state !== self::MATCHSTATE_WAITING_FOR_PLAYERJOINS){
                throw new GameException("MATCH.CLOSED");
            }
            $user->joinedMatch()->associate($match);
            $user->save();
            
            if(count($match->joinedUsers()) >= $match->maxusers){
                $this->startMatch($match);
            }

            
    }
    
    public function startMatch($match){
        
            $match->state = self::MATCHSTATE_START;
            $match->save();
                
    }
    
    public function rejectInvitation($id, $user) {
        
        $join = Invitation::find($id)->first();
        Log::info("Rejecting: ");
        Log::info($join);
        
        if($user->id == $join->user->id){
            
            $join->status = "rejected";
            $join->save();
            
        }
        else {
            
            // throw Exception?
            
        }
        
    }
    
    public function rejectAllInvitationsForUser($user) {
        
        foreach($user->invitedToJoin as $invitation){
            $this->rejectInvitation($invitation->id, $user);
        }
        
    }
    
    public function deleteInvitation($id, $user) {
        
        $join = Invitation::find($id)->first();
        
        if($user->id == $join->invitedBy->id){
            
            $join->delete();
            
        }
        else {
            
            // throw Exception?
            
        }
        
    }
    
    public function resolveInvitation($id, $user) {
        
        $join = Invitation::find($id);
        
        if($user->id == $join->user_id){
            
            $join->delete();
            
        }
        else {
            
            // throw Exception?
            
        }
        
    }
    
    
    public function saveAdministratedMatch($match, $user, $inputs) {
        
            $invitedPlayersInput = str_replace(", ", ",", $inputs['invited_players']);
            $invitedPlayerNames = explode(",", $invitedPlayersInput);
            $invitationMessage = $inputs['message'];
            foreach ($invitedPlayerNames as $invitedPlayerName){
                $foundPlayer = User::where("name", $invitedPlayerName)->first();
                if($foundPlayer){
                    $userJoinMatch = new Invitation();
                    $userJoinMatch->status = "invited";
                    $userJoinMatch->invitedBy()->associate($user);
                    $userJoinMatch->user()->associate($foundPlayer);
                    $userJoinMatch->match()->associate($match);
                    $userJoinMatch->message = $invitationMessage;
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
    
}
