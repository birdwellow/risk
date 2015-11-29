<?php namespace Game\Model;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model {

    protected $fillable = ['points'];

    
    public function toArray(){
        return json_decode($this->points);
    }
    
        
    public function match()
    {
        return $this->belongsTo('Game\Model\Match', 'match_id', 'id');
    }

}
