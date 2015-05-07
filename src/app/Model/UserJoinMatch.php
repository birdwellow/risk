<?php namespace Game\Model;

use Illuminate\Database\Eloquent\Model;

class UserJoinMatch extends Model {

    protected $table = 'user_join_match';

    protected $fillable = ['status', 'basecolor'];
    
    
    public function invitedBy()
    {
        return $this->belongsTo('Game\User', 'invited_by_user_id', 'id');
    }
    
    
    public function user()
    {
        return $this->belongsTo('Game\User');
    }
    
    
    public function match()
    {
        return $this->belongsTo('Game\Model\Match');
    }
}
