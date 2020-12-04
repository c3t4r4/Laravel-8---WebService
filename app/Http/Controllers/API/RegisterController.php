<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register(UserRequest $request)
    {
        $user = new User;

        $data = $request->all();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);

            $user->fill($data);

            if(!$user->save()){
                return response()->json('Erro - Ao criar usuário!', 403);
            }

            //$user->token = $user->createToken($user->email)->accessToken;

            return response()->json($user, 201);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(!Auth::validate($credentials)){
            $json['message'] = [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Ooops, usuário e senha não conferem!',
            ];
            return response()->json($json);
        }

        Auth::attempt($credentials);

        $user  = Auth::user();

        $token = $user->createToken($user->email)->accessToken;

        return response()->json(['token' => $token], 200);
    }
}
