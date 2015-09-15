<?php namespace Game\Model;

use Illuminate\Database\Eloquent\Model;

class Region extends Model {

    protected $fillable = [
        'name',
        'troops',
        'svgdata',
        'centerx',
        'centery',
        'labelcenterx',
        'labelcentery',
        'angle',
        'pathdata',
    ];

    public function toArray()
    {
        $array = parent::toArray();
        
        unset($array["owner_id"]);
        unset($array["card_owner_id"]);
        unset($array["continent_id"]);
        unset($array["created_at"]);
        unset($array["updated_at"]);
        
        $array["continent"] = "[continents:id=" . $this->continent->id . "]";
        $array["owner"] = "[players:id=" . ( $this->owner ? $this->owner->id : null ) . "]";
        $array["cardOwner"] = "[players:id=" . ( $this->cardOwner ? $this->cardOwner->id : null ) . "]";
        $array["neighbors"] = [];
        foreach($this->neighbors as $neighborRegion){
            array_push($array['neighbors'], "[regions:id=" . $neighborRegion->id . "]");
        }
        
        return $array;
    }
                        
        
    public function continent()
    {
        return $this->belongsTo('Game\Model\Continent', 'continent_id', 'id');
    }
                        
        
    public function owner()
    {
        return $this->belongsTo('Game\User', 'owner_id', 'id');
    }
                        
        
    public function cardOwner()
    {
        return $this->belongsTo('Game\User', 'card_owner_id', 'id');
    }
                        
        
    public function neighbors()
    {
        return $this->belongsToMany('Game\Model\Region', 'region_neighborregion', 'region_id', 'neighborregion_id');
    }

}
