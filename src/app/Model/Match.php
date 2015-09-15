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
        
        unset($array["id"]);
        unset($array["joinid"]);
        unset($array["map_id"]);
        unset($array["public"]);
        unset($array["thread_id"]);
        unset($array["active_player_id"]);
        unset($array["created_by_user_id"]);
        unset($array["created_at"]);
        unset($array["updated_at"]);
        
        $array['activePlayer'] = "[players:id=" . ( $this->activePlayer ? $this->activePlayer->id : null ) . "]";
        $array['createdBy'] = "[players:id=" . $this->createdBy->id . "]";
        $array['continents'] = $this->continents;
        $array['regions'] = $this->regions();
        $array['players'] = $this->joinedUsers;
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
    
    
    public function regions()
    {
        $regions = [];
        foreach ($this->continents as $continent) {
            foreach ($continent->regions as $region) {
                array_push($regions, $region);
            }
        }
        return $regions;
    }
    
    
    public function activePlayer()
    {
        return $this->belongsTo('Game\User', 'active_player_id', 'id');
    }
    
}
