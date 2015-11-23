<?php namespace Game\Model;

use Illuminate\Database\Eloquent\Model;

class Continent extends Model {

    protected $fillable = ['name', 'colorscheme'];
        

    public function toArray()
    {
        $array = parent::toArray();
        
        unset($array["match_id"]);
        unset($array["owner_id"]);
        unset($array["created_at"]);
        unset($array["updated_at"]);
        
        $array['regions'] = [];
        foreach($this->regions as $region){
            array_push($array['regions'], $region->socketIdentifier());
        }
        $array["owner"] = ( $this->owner ? $this->owner->socketIdentifier() : null );
        return $array;
    }
    
    
    public function socketIdentifier() {
        return "[continents:id=" . $this->id . "]";
    }
    
        
    public function match()
    {
        return $this->belongsTo('Game\Model\Match', 'match_id', 'id');
    }
                        
        
    public function owner()
    {
        return $this->belongsTo('Game\User', 'owner_id', 'id');
    }
    

    public function regions()
    {
        return $this->hasMany('Game\Model\Region', 'continent_id', 'id');
    }

}
