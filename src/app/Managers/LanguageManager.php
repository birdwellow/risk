<?php namespace Game\Managers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

/**
 * Description of MatchManager
 *
 * @author fvo
 */
class LanguageManager {
    
    
    protected $sessionLangToken = "language";
    
    protected $fallbackLocale = "en";

    protected $allowedLocales = ["en", "de"];
    
    
    public function setGlobalLocale($lang, $user){
        
            if(in_array($lang, $this->allowedLocales)){
                Session::set($this->sessionLangToken, $lang);
                if($user){
                    $user->language = $lang;
                    $user->save();
                }
            }
            
    }
    
    
    public function setUserLocale($user) {
        
            if($user && $user->language){
                Session::set($this->sessionLangToken, $user->language);
            }
        
    }
    
    
    public function setFallbackLocale(){
            
            if(!Session::get($this->sessionLangToken)){
                Session::set($this->sessionLangToken, $this->fallbackLocale);
            }
            
    }
    
    
    public function setAppLocale() {
        
            App::setLocale(Session::get($this->sessionLangToken));
        
    }
    
}
