<?php namespace Game\Providers;

use Illuminate\Support\ServiceProvider;

class ManagerServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
            $this->app->bind("MatchManager", function(){
                return new \Game\Managers\MatchManager();
            });
            
            $this->app->bind("LanguageManager", function(){
                return new \Game\Managers\LanguageManager();
            });
            
            $this->app->bind("OptionsManager", function(){
                return new \Game\Managers\OptionsManager();
            });
            
            $this->app->bind("JsonRestManager", function(){
                return new \Game\Managers\JsonRestManager();
            });
	}

}
