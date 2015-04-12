<?php namespace Game\Http\Controllers;

use \Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

class UserController extends Controller {
    
	public function __construct()
	{
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
		return view('user.options')
                        ->with("colorSchemeValues", ['baroque' => "Baroque", 'modern' => "Modern"])
                        ->with("languageValues", ['en' => "English", 'de' => "German"]);
	}
        
        public function optionsSave()
        {
                $user = Auth::user();
                $user->name = Request::input('name');
                $user->email = Request::input('email');
                $user->colorscheme = Request::input('colorScheme');
                Session::set("colorscheme", Request::input('colorScheme'));
                
                if(Request::hasFile('avatar') && Request::file('avatar')->isValid()){
                    $oldFile = $user->avatarfile;
                    $file = Request::file('avatar');
                    $path = app_path() . "/../public/img/avatars";
                    $storeFileName = $user->name . "_" . uniqid() . "." . $file->getClientOriginalExtension();
                    Request::file('avatar')->move($path, $storeFileName);
                    $user->avatarfile = $storeFileName;
                    
                    if(File::exists($path."/".$oldFile)){
                        File::delete($path."/".$oldFile);
                    }
                }
                
                $user->save();
                
                return $this->options()->with("success", true);
        }

}
