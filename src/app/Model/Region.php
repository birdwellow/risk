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
                        
        
    public function continent()
    {
        return $this->belongsTo('Game\Model\Continent', 'continent_id', 'id');
    }
                        
        
    public function owner()
    {
        return $this->belongsTo('Game\Model\User', 'owner_id', 'id');
    }
                        
        
    public function cardOwner()
    {
        return $this->belongsTo('Game\Model\User', 'card_owner_id', 'id');
    }
                        
        
    public function neighbors()
    {
        return $this->belongsToMany('Game\Model\Region', 'region_neighborregion', 'region_id', 'neighborregion_id');
    }

}
