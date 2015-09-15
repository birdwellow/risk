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
            array_push($array['regions'], "[regions:id=" . $region->id . "]");
        }
        $array["owner"] = "[players:id=" . ( $this->owner ? $this->owner->id : null ) . "]";
        return $array;
    }
    
        
    public function match()
    {
        return $this->belongsTo('Game\Model\Match', 'match_id', 'id');
    }
    

    public function regions()
    {
        return $this->hasMany('Game\Model\Region', 'continent_id', 'id');
    }

}
