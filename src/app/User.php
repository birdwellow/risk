<?php namespace Game;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Ratchet\ConnectionInterface;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];
        
        
        protected $socket = null;
        
        public $theme = "default";
        
        public function getSocket(){
            return $this->socket;
        }
        
        public function setSocket(ConnectionInterface $conn){
            $this->socket = $conn;
        }

        public function createdMatches()
        {
            return $this->hasMany('Game\Model\Match', 'created_by_user_id', 'id');
        }
        
        
        public function joinedMatch()
        {
            return $this->belongsTo('Game\Model\Match', 'joined_match_id', 'id');
        }

}
