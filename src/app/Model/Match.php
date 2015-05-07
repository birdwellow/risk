<?php namespace Game\Model;

use Illuminate\Database\Eloquent\Model;
use SplObjectStorage;
use Game\User;
use Ratchet\ConnectionInterface;

class Match extends Model {

    protected $fillable = ['cardChangeBonusLevel', 'map', 'name'];
    
    protected $connectedUsers;


    public function __construct(){
        parent::__construct();
        $this->connectedUsers = new SplObjectStorage();
    }
    
    
    public function connectUser(User $user){
        if(!$this->connectedUsers->contains($user)){
            $this->connectedUsers->attach($user);
        }
    }
    
    
    public function disconnectUser(User $user){
        if($this->connectedUsers->contains($user)){
            $this->connectedUsers->detach($user);
        }
    }
    
    
    public function getConnectedUsers(){
        return $this->connectedUsers;
    }
    

    public function joinedUsers()
    {
        return $this->hasMany('Game\User', 'joined_match_id', 'id');
    }
    
    
    public function createdBy()
    {
        return $this->belongsTo('Game\User', 'created_by_user_id', 'id');
    }

        
    public function invitedFromJoins()
    {
        return $this->hasMany('Game\Model\UserJoinMatch', 'invited_by_user_id', 'id');
    }
}
