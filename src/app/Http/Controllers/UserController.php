<?php namespace Game\Http\Controllers;

use \Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Game\Managers\LanguageManager;
use Game\Managers\OptionsManager;
use Game\Exceptions\GameException;
use Game\Handlers\Messages\ErrorFeedback;
use Game\Handlers\Messages\SuccessFeedback;

class UserController extends Controller {
    
        protected $languageManager;
        
        protected $optionsManager;


        public function __construct(LanguageManager $languageManager, OptionsManager $optionsManager)
	{
		$this->optionsManager = $optionsManager;
                $this->languageManager = $languageManager;
                $this->middleware('auth');
	}

	public function profile()
	{
                $user = Auth::user();
		return view('user.profile')->with("user", $user);
	}
        
        public function profileSave()
        {
                return $this->profile();
        }

        public function options()
	{
		return view('user.options');
	}
        
        public function optionsSave()
        {
                $user = Auth::user();
                $optionInputs = array(
                    "username" => Request::input('name'),
                    "email" => Request::input('email'),
                    "avatar" => Request::file('avatar')
                );
                
                try{
                    
                    $this->optionsManager->saveOptions($user, $optionInputs);

                    return $this->options()->with(
                            "message",
                            new SuccessFeedback("message.success.userinput.save")
                    );
                    
                } catch (GameException $ex) {
                    return redirect()->back()->with(
                            "message",
                            new ErrorFeedback($ex->getUIMessageKey(), $ex->getCustomData())
                    );
                }
        }

}
