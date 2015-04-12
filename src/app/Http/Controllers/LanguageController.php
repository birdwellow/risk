<?php namespace Game\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller {

    
        protected $allowedLocales = ["en", "de"];
    
    
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
	public function __construct()
	{
            
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function switchTo($lang)
	{
            if(in_array($lang, $this->allowedLocales)){
                Session::set("language", $lang);
                $user = Auth::user();
                if($user){
                    $user->language = $lang;
                    $user->save();
                }
		return redirect()->back();
            }
            return redirect()->back();
	}

}
