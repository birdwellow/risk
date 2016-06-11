<?php namespace Game\Http\Middleware;

use Closure;

use Game\Managers\AccountManager;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserSessionMiddleware {
    
    
        protected $accountManager;


        public function __construct(AccountManager $accountManager) {

            $this->accountManager = $accountManager;
            
        }

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
            
                $userLocale = $this->accountManager->getUserAppLocale(Auth::user());
                App::setLocale($userLocale);
                
                $this->accountManager->getUserTheme(Auth::user());
                //Session::put('theme', $userTheme);
                
		return $next($request);
                
	}

}
