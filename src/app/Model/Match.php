<?php namespace Game\Model;

use Illuminate\Database\Eloquent\Model;

class Match extends Model {

    protected $fillable = ['cardChangeBonusLevel', 'map', 'name', 'maxUsers', 'roundphase'];
    
    public $translations, $me;

    public function toArray(){
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
        $array['regions'] = $this->regions;
        $array['connections'] = $this->connections;
        $array['players'] = $this->joinedUsers;
        $array['thread'] = $this->thread;
        
        $array['me'] = $this->me;
        $array['translations'] = $this->translations;
        
        return $array;
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
    

    public function connections()
    {
        return $this->hasMany('Game\Model\Connection', 'match_id', 'id');
    }
    
    
    public function regions()
    {
        return $this->hasManyThrough('Game\Model\Region', 'Game\Model\Continent');
    }
    
    
    public function activePlayer()
    {
        return $this->belongsTo('Game\User', 'active_player_id', 'id');
    }
    
}
