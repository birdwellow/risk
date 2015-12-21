<?php namespace Game;

use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword, Messagable;

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
	protected $fillable = [
            'name',
            'email',
            'password',
            'language',
            'matchnotification',
            'matchescreated',
            'matchesplayed',
            'matcheswon'
        ];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];
        
        
        public function toArray(){
            
            $array = parent::toArray();
            
            unset($array["created_at"]);
            unset($array["updated_at"]);
            unset($array["joined_match_id"]);
            unset($array["joinid"]);
            
            $array["regions"] = [];
            foreach($this->regions as $region){
                array_push($array["regions"], $region->socketIdentifier());
            }
            $array["cards"] = [];
            foreach($this->cards as $cardRegion){
                array_push($array["cards"], $cardRegion->socketIdentifier());
            }
            $array["continents"] = [];
            foreach($this->continents as $continent){
                array_push($array["continents"], $continent->socketIdentifier());
            }
            
            return $array;
        }
    
    
        public function socketIdentifier() {
            return "[players:id=" . $this->id . "]";
        }

        
        public function createdMatch()
        {
            return $this->hasOne('Game\Model\Match', 'created_by_user_id', 'id');
        }
        
        
        public function joinedMatch()
        {
            return $this->belongsTo('Game\Model\Match', 'joined_match_id', 'id');
        }
        
        
        public function isActive()
        {
            $activePlayer = $this->belongsTo('Game\Model\Match', 'joined_match_id', 'id');
            return ( $activePlayer->id == $this->id );
        }
    

        public function regions()
        {
            return $this->hasMany('Game\Model\Region', 'owner_id', 'id');
        }
    

        public function continents()
        {
            return $this->hasMany('Game\Model\Continent', 'owner_id', 'id');
        }
    

        public function cards()
        {
            return $this->hasMany('Game\Model\Region', 'card_owner_id', 'id');
        }

}
