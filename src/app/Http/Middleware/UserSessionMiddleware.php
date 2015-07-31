<?php namespace Game\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Game\Managers\AccountManager;

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
            
                $this->accountManager->setAppLocale(Auth::user());
                
		return $next($request);
                
	}

}
