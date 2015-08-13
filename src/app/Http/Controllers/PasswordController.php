<?php namespace Game\Http\Controllers;

use Game\Http\Controllers\Controller;
use Game\Managers\AccountManager;
use Game\Handlers\Messages\SuccessFeedback;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\PasswordBroker;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PasswordController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	/**
	 * The account manager implementation.
	 *
	 * @var AccountManager
	 */
	protected $accountManager;

	/**
	 * The password broker implementation.
	 *
	 * @var PasswordBroker
	 */
	protected $passwords;

	/**
	 * Create a new password controller instance.
	 *
	 * @param  \Game\Managers\AccountManager  $accountManager
	 * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
	 * @return void
	 */
	public function __construct(AccountManager $accountManager, PasswordBroker $passwords) {
            
		$this->accountManager = $accountManager;
		$this->passwords = $passwords;

		$this->middleware('guest');
	
        }

	/**
	 * Display the form to request a password reset link.
	 *
	 * @return Response
	 */
	public function getEmail() {
            
		return view('auth.password');
                
	}

	/**
	 * Send a reset link to the given user.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function postEmail(Request $request) {
            
                $email = $request->get("user_email");
            
		$this->check(['user_email' => $email], "PASSWORDRESET.EMAIL.NOT.SENT");
                
                $result = $this->accountManager->sendPasswordResetLink($email);
                
		return redirect()->back()->with(
                        "message",
                        new SuccessFeedback(trans($result))
                );
                
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset($token = null) {
            
		if (is_null($token)) {
			throw new NotFoundHttpException;
		}

		return view('auth.reset')->with('token', $token);
                
	}

	/**
	 * Reset the given user's password.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function postReset(Request $request) {
            
                $token = $request->get("token");
                $email = $request->get("user_email");
                $password = $request->get("new_user_password");
                $passwordConfirmation = $request->get("new_user_password_confirmation");
            
		$this->validator->check([
			'password_reset_token' => $token,
			'user_email' => $email,
			'new_user_password' => $password,
			'new_user_password_confirmation' => $passwordConfirmation,
		], "PASSWORD.NOT.CHANGED");
                
                $result = $this->accountManager->resetPassword($token, $email, $password, $passwordConfirmation);
                
                return redirect($this->redirectPath())->with(
                        "message",
                        new SuccessFeedback(trans($result))
                );
                
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

}
