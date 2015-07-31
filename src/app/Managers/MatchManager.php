<?php namespace Game\Managers;

use Game\User;
use Game\Model\Match;
use Game\Exceptions\GameException;
use \Illuminate\Support\Facades\Session;
use \Illuminate\Support\Facades\Log;
use Game\Model\UUID;
use Game\Model\Invitation;
use Game\Model\Map;
use Illuminate\Support\Facades\Validator;

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
    
    public function checkUserMayJoinMatch($userId, $matchId) {
        
            $match = Match::find($matchId);
            if($match->closed){
                $userIsMatchCreator = ($match->createdBy->id == $userId);
                $userInvitedForMatch = $this->isUserInvitedForMatch($userId, $matchId);
                
                if(!$userIsMatchCreator && !$userInvitedForMatch){
                    throw new GameException("USER.NOT.INVITED.TO.CLOSED.MATCH");
                }
            }
            
            $user = Match::find($userId);
            if($user && $user->joinedMatch !== null){
                throw new GameException("USER.ALREADY.JOINED");
            }
        
    }
    
    
    public function getAllMatches(){
        
        return Match::all();
        
    }
    
    
    public function createMatch($user, $inputs) {
        
            $validator = Validator::make(
                [
                    'name' => $inputs['name'],
                    'maxusers' => $inputs['maxusers'],
                    'mapName' => $inputs['mapName'],
                ],
                [
                    'name' => 'required|min:6|max:32|unique:matches',
                    'maxusers' => 'required|integer|min:2|max:6',
                    'mapName' => 'required|in:'.  implode(",", $this->getMapNames()),
                ]
            );
            if($validator->fails()){
                \Illuminate\Support\Facades\Log::info($validator->messages());
                throw new GameException(
                        "USER.CREATE.MATCH.WRONG.PARAMETERS",
                        $validator->messages()
                );
            }

            $match = new Match();
            $match->name = $inputs['name'];
            $match->createdBy()->associate($user);
            $match->closed = ($inputs["closed"] !== null);
            $match->maxusers = $inputs["maxusers"];
            $map = Map::where("name", $inputs['mapName'])->first();
            $match->map()->associate($map);
            
            $match->save();
            

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
            
            $user->joinedMatch()->associate($match);
            $user->save();
            return $match;
            
    }
    
    public function deleteMatch($id) {
        
            $match = Match::find($id);
            if(!$match){
                throw new GameException("MATCH.NOT.FOUND");
            } else {
                foreach ($match->joinedUsers() as $joinedUser) {
                    $joinedUser->joinedMatch()->detach();
                }
                foreach ($this->getAllInvitationsForMatch($id) as $invitation){
                    $invitation->delete();
                }
                
                $match->delete();
            }
        
    }
    
    public function joinMatch($matchId, $user){
            
            $invitation = $this->getNewInvitationForUserAndMatch($user->id, $matchId);
        
            if($invitation !== null){
                $this->resolveInvitation($invitation->id, $user);
            }
            $this->rejectAllInvitationsForUser($user);

            $match = Match::find($matchId);
            $user->joinedMatch()->associate($match);
            $user->save();
            
    }
    
    public function goToMatch($id, $user) {
        
            $match = Match::find($id);
            if($match == null){
                throw new GameException("MATCH.NOT.FOUND");
            } else {
                $user->joinid = UUID::v5("1546058f-5a25-4334-85ae-e68f2a44bbaf", $user->name);
                $user->save();
                
                return $match;
            }
        
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
    
    public function getNewInvitationsForUser($userId){
        
        return Invitation::where("user_id", "=", $userId)
                        ->where("status", "=", "invited")->get();
        
    }
    
    public function getAllInvitationsForMatch($matchId){
        
        return Invitation::where("match_id", "=", $matchId)->get();
        
    }
    
    public function getNewInvitationForUserAndMatch($userId, $matchId){
        
        return Invitation::where("user_id", "=", $userId)
                        ->where("status", "=", "invited")
                        ->where("match_id", "=", $matchId)->first();
        
    }
    
    public function isUserInvitedForMatch($userId, $matchId){
        
        return (Invitation::where("user_id", "=", $userId)
                        ->where("status", "=", "invited")
                        ->where("match_id", "=", $matchId)->first() !== null);
        
    }
    
    public function getRejectedInvitationsForUser($userId){
        
        return Invitation::where("invited_by_user_id", "=", $userId)
                        ->where("status", "=", "rejected")->get();
        
    }
    
}
