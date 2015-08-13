<?php namespace Game\Http\Controllers;

use Game\Http\Controllers\Controller;
use Game\Managers\AccountManager;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users.
	|
	*/

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Game\Managers\AccountManager  $accountManager
	 * @return void
	 */
	public function __construct(AccountManager $accountManager) {
            
                $this->accountManager = $accountManager;

		$this->middleware('guest', ['except' => 'logout']);
                
	}

	/**
	 * The accountManager implementation.
	 *
	 * @var \Game\Managers\AccountManager
	 */
	protected $accountManager;

	/**
	 * Show the application registration form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showRegister() {
            
		return view('auth.register');
                
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function register(Request $request) {
            
                $username = $request->get("new_user_name");
                $email = $request->get("new_user_email");
                $password = $request->get("new_user_password");
                $passwordConfirmation = $request->get("new_user_password_confirmation");
                
                $this->check([
                    "new_user_name" => $username,
                    "new_user_email" => $email,
                    "new_user_password" => $password,
                    "new_user_password_confirmation" => $passwordConfirmation,
                ], "REGISTRATION.ERROR");
                
                $newUser = $this->accountManager->registerNewUserWith($username, $email, $password, $passwordConfirmation);
            
            	Auth::login($newUser);

		return redirect($this->redirectPath());
                
	}

	/**
	 * Show the application login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showLogin() {
            
		return view('auth.login');
                
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function login(Request $request) {
            
                $email = $request->get("user_email");
                $password = $request->get("user_password");
                $remember = $request->has('user_remember_login');
                
                $attributes = [
                    "user_email" => $email,
                    "user_password" => [
                        $password,
                        "auth:" . $email
                    ],
                ];
                $this->check($attributes, "LOGIN.ERROR");
                
                $credentials = [
                    "email" => $email,
                    "password" => $password
                ];
                
                Auth::attempt($credentials, $remember);
                
                return redirect()->intended($this->redirectPath());
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function logout() {
            
		Auth::logout();

		return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
                
	}

	/**
	 * Get the post register / login redirect path.
	 *
	 * @return string
	 */
	public function redirectPath() {
            
		if (property_exists($this, 'redirectPath')) {
			return $this->redirectPath;
		}

		return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
                
	}

	/**
	 * Get the path to the login route.
	 *
	 * @return string
	 */
	public function loginPath() {
            
		return property_exists($this, 'loginPath') ? $this->loginPath : '/auth/login';
                
	}

}
