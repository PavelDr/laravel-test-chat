<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use App\Message;
use Illuminate\Http\Request;
use App\Services\RegistrationService;
use Auth;
use View;

/**
 * Class AuthController
 * @package App\Http\Controllers\Auth
 */
class AuthController extends Controller
{
    /**
     * Go to list page after wrong login
     * @var string
     */
    protected $loginPath = '/';

    /**
     * Go to list after success login or registration
     * @var string
     */
    protected $redirectPath = 'chat/list';
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);
        $email = $credentials['email'];
        $password = $credentials['password'];

        if (Auth::attempt(array('email' => $email, 'password' => $password), $request->has('remember'))
            || Auth::attempt(array('username' => $email, 'password' => $password), $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect($this->loginPath())
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }

    /**
     * Handle a registration request for the application.
     * Rewrite standart laravel5 post registration
     *
     * @param Request $request
     * @param RegistrationService $registrationService
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postRegister(Request $request, RegistrationService $registrationService)
    {
         $this->validate($request, [
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = $registrationService->registration($request->all());

        //Authorize user
        Auth::login($user);

        return redirect('chat/list');
    }
}
