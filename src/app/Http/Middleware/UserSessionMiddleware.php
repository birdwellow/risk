<?php namespace Game\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Game\Managers\LanguageManager;
use Game\Managers\OptionsManager;

class UserSessionMiddleware {
    
    
        protected $languageManager;
        
        protected $optionsManager;


        public function __construct(LanguageManager $languageManager, OptionsManager $optionsManager) {

            $this->languageManager = $languageManager;
            $this->optionsManager = $optionsManager;
            
        }

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{       
                $this->languageManager->setUserLocale(Auth::user());
                $this->languageManager->setFallbackLocale();
                $this->languageManager->setAppLocale();
                
		return $next($request);
	}

}
