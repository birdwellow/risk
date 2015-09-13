<?php namespace Game\Model;

use Illuminate\Database\Eloquent\Model;

class Continent extends Model {

    protected $fillable = ['name', 'colorscheme'];
        

    public function toArray()
    {
        $array = parent::toArray();
        $array['regions'] = $this->regions;
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
