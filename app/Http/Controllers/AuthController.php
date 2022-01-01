<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    private $expired_time = 60 * 24;#1 dia

    public function __construct() {
        $this->middleware(
            'auth:api', ['except' => ['login']]
        );
    }

    public function login(Request $request) {
        $credentials = $request->only(['mail', 'pass']);
        $validator = User::validate($credentials);
        if( $validator->fails() ) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
        auth()->factory()->setTTL($this->expired_time);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED );
        }
        return $this->respondWithToken($token);
    }

    public function me() {
        return response()->json(auth()->user());
    }

    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
