<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use App\User;
use Auth;
use Illuminate\Support\Facades\Request;


class LoginApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['index', 'refreshToken',  'logout']]);
    }

    /**
    * Login
    *
    * @return void
    */
    public function index()
    {
        $credentials = Request::only('email', 'password');
        //$credentials['status'] = 1;

        if (! $token = JWTAuth::attempt($credentials)) {
            abort(401, 'Unauthorized action.');
            
            
        }


        $user = Auth::User();
        $company = $user->company;
        if ($company && !$company->active) {
           // throw new ErrorCepymeException('company_not_active', 401);
        }
        $data = array(
            'user' => $user,
            'token' => $token
        );
        return response()->json($data);
    }

    /**
    * RefreshToken
    *
    * @return void
    */
    public function refreshToken()
    {
        $token = \JWTAuth::getToken();
        if (!$token) {
            throw new ErrorCepymeException('token_missing', 401);
        }
        $token = \JWTAuth::refresh($token);
        return response()->json(compact('token'));
    }

    /**
    * Logout
    *
    * @return void
    */
    public function logout()
    {
        \Session::flush();
        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

}
