<?php namespace Game\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UserSessionMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
                if(!Session::has("colorscheme")){
                    $colorscheme = "modern";
                    if(Auth::user()){
                        $colorscheme = Auth::user()->colorscheme;
                    }
                    Session::set("colorscheme", $colorscheme);
                }
                if(Auth::user()){
                    Session::set("colorscheme", Auth::user()->colorscheme);
                }
                
                $language = Session::get("language");
                if($language == null){
                    Session::set("language", "en");
                }
                if(Auth::user()){
                    $language = Auth::user()->language;
                    Session::set("language", $language);
                }
                App::setLocale($language);
                
		return $next($request);
	}

}
