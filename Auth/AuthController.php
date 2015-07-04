<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Traits\AuthenticatesUsers;
use App\User;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;

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

    use AuthenticatesUsers;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
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
            'login' => 'string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:3',
            'country' => 'required',

            'options' => '',
            'description' => 'string|max:5000',
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
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $fields = $request->only([
            'name',
            'email',
            'country',
            'password',
            'password_confirmation'
        ]);

        $validator = $this->validator($fields);

        if ($validator->fails()) {
            return \Response::json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $user = $this->create($request->all());
        \Auth::login($user);
        if (! $request->ajax()){
            return redirect($this->redirectPath())->with(['message' => "You're finally registered, $user->name"]);
        }

        return \Response::json([
            'success' => true,
        ]);
    }
}
