<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Http\Controllers;
use App\AuthenticateUser;

class AuthController extends Controller
{
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
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Redirect the user to the facebook authentication page.
     *
     * @return Response
     */
    function authFacebook(AuthenticateUser $authenticateUser, Request $request){

        // Redirect to home if user cancelled facebook login
        if ($request->has('error')) {
            return redirect('/');
        }

        // Process to/with facebook callback
        return $authenticateUser->execute($request->has('code'), $this, 'facebook');
    }

    /**
     * Redirect logged in user to dashboard
     */
    public function userHasLoggedIn($user)
    {
        $type = $user->type;

        switch ($type) {
            case 'admin':
                return redirect(route('admin::dashboard'));
                break;
            case 'branch':
                return redirect(route('brance::dashboard'));
                break;
            default:
                return redirect(route('customer.dashboard'));
                break;
        }
    }

}
