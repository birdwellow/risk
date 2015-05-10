<?php namespace Game\Managers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Game\Exceptions\GameException;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class OptionsManager {
    
    
    protected $sessionColorschemeToken = "colorscheme";
    
    protected $fallbackColorscheme = "classic";

    protected $allowedColorschemes = [
        "classic" => "Classic",
        "coldwar" => "Cold War",
    ];
    
    
    public function getAllowedColorschemes() {
        
            return $this->allowedColorschemes;
        
    }
    
    
    public function isCollorschemeAllowed($colorscheme) {
        
            return isset($this->allowedColorschemes[$colorscheme]);
        
    }
    
    
    public function setUserColorscheme($user) {
        
            if($user && $user->colorscheme){
                Session::set($this->sessionColorschemeToken, $user->colorscheme);
            }
        
    }
    
    
    public function setFallbackColorscheme(){
            
            if(!Session::get($this->sessionColorschemeToken)){
                Session::set($this->sessionColorschemeToken, $this->fallbackColorscheme);
            }
            
    }
    
    
    public function setDefaultColorscheme(){
            
            Session::set($this->sessionColorschemeToken, $this->fallbackColorscheme);
            
    }
    
    
    public function saveOptions($user, $optionInputs){
        
            $validator = Validator::make(
                [
                    'username' => $optionInputs['username'],
                    'email' => $optionInputs['email'],
                    'colorscheme' => $optionInputs['colorscheme'],
                ],
                [
                    'username' => 'required|min:5',
                    'email' => 'required|email',
                    'colorscheme' => 'in:'.  implode(",", array_keys($this->allowedColorschemes)),
                ]
            );
            if($validator->fails()){
                \Illuminate\Support\Facades\Log::info($validator->messages());
                throw new GameException(
                        "USER.INVALID.OPTIONS",
                        $validator->messages()
                );
            }
        
            $user->name = $optionInputs['username'];
            $user->email = $optionInputs['email'];
            $user->colorscheme = $optionInputs['colorscheme'];
            
            $user->save();
            
            
            $avatarFile = $optionInputs['avatar'];
            if($avatarFile && $avatarFile->isValid()){
                $oldFile = $user->avatarfile;
                $path = app_path() . "/../public/img/avatars";
                $storeFileName = $user->name . "_" . uniqid() . "." . $avatarFile->getClientOriginalExtension();
                $avatarFile->move($path, $storeFileName);
                $user->avatarfile = $storeFileName;

                if(File::exists($path."/".$oldFile)){
                    File::delete($path."/".$oldFile);
                }
            }
        
    }
    
    
}
