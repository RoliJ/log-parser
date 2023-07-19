<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use Laravel\Passport\TokenRepository;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        // Create a new user with the provided data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate an access token for the user
        $token = $user->createToken('AccessToken')->accessToken;

        // Return the access token in the response
        return response()->json(['token' => $token], 201);
    }

    /**
     * Log in an existing user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        // Retrieve the user's credentials from the request
        $credentials = $request->only('email', 'password');

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Authentication successful
            $user = $request->user();
            // Generate a new access token for the user
            $token = $user->createToken('AccessToken')->accessToken;

            // Return the access token in the response
            return response()->json(['token' => $token]);
        }

        // Authentication failed, return error response
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $tokenRepository = app(TokenRepository::class);

        $user = auth('api')->user();

        if ($user) {
            $tokenRepository->revokeAccessToken($user->token()->id);
            $tokens =  $user->tokens->pluck('id');
            Token::whereIn('id', $tokens)
                ->update(['revoked'=> true]);

            RefreshToken::whereIn('access_token_id', $tokens)->update(['revoked' => true]);

            // Invalidate the user's session
            $request->session()->invalidate();

            // Return success message
            return response()->json(['message' => 'Logged out successfully']);
        } else {
            return response()->json(['message' => 'Already Logged out']);
        }
    }
}
