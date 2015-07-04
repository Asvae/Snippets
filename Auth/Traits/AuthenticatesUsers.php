<?php namespace App\Http\Controllers\Auth\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

trait AuthenticatesUsers
{
    use RedirectsUsers;

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {

        $credentials = $request->only('email', 'password');
        $credentials['paused'] = 0;

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $loggedIn = Auth::attempt($credentials, $request->has('remember'));

        if ($loggedIn){
            if ($request->ajax()){
                return \Response::json(['success' => true]);
            }
            return redirect('/home')->with(['message' => 'You successfully invaded the system.']);
        }else{
            if ($request->ajax()){
                return \Response::json([
                    'success' => false,
                    'errors' => ['These credentials do not match our records.'],
                ]);
            }
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        Auth::logout();

        return redirect('/home');
    }
}
