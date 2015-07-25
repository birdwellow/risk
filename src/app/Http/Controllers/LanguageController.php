<?php namespace Game\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Game\Managers\LanguageManager;

class LanguageController extends Controller {

    
        protected $languageManager;
    
    
	/*
	|--------------------------------------------------------------------------
	| Language Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for switching the App locale
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(LanguageManager $languageManager) {
                
                $this->languageManager = $languageManager;
                
	}

        
        
	/**
	 * Set the current locale and return
	 *
	 * @return Response
	 */
	public function switchTo($lang) {
            
                $this->languageManager->setGlobalLocale($lang, Auth::user());
                return redirect()->back();
                
	}

}
