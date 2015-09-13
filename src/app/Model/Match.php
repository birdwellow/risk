<?php namespace Game\Model;

use Illuminate\Database\Eloquent\Model;
use SplObjectStorage;
use Game\User;

class Match extends Model {

    protected $fillable = ['cardChangeBonusLevel', 'map', 'name', 'maxUsers'];
    
    protected $connectedUsers;

    public function toArray()
    {
        $array = parent::toArray();
        
        $array['activePlayer'] = $this->activePlayer;
        $array['continents'] = $this->continents;
        $array['createdBy'] = $this->createdBy;
        $array['joinedUsers'] = $this->joinedUsers;
        $array['thread'] = $this->thread;
        
        return $array;
    }
    
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
    
    
    public function thread()
    {
        return $this->belongsTo('Cmgmyr\Messenger\Models\Thread', 'thread_id', 'id');
    }
    

    public function continents()
    {
        return $this->hasMany('Game\Model\Continent', 'match_id', 'id');
    }
    
    
    public function activePlayer()
    {
        return $this->belongsTo('Game\User', 'active_player_id', 'id');
    }
    
}
