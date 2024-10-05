<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use App\Models\User;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);

            $token = auth('api')->login($user);

            return response()->json([
                'success' => true,
                'message' => 'Usuário registrado com sucesso.',
                'data' => [
                    'access_token' => $token,
                    'user' => $user,
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar usuário.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function login()
    {
        try {
            $credentials = request(['email', 'password']);

            if (! $token = auth('api')->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas.',
                    'data' => null
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso.',
                'data' => [
                    'access_token' => $token,
                    'user' => auth()->user()
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar login.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function validateToken(Request $request)
    {
        try {
            $request->validate([
                'access_token' => 'required|string'
            ]);

            $token = $request->input('access_token');

            if (! $user = JWTAuth::setToken($token)->getPayload()->get('sub')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido ou expirado.',
                    'data' => null
                ], 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();

            return response()->json([
                'success' => true,
                'message' => 'Token válido.',
                'data' => [
                    'access_token' => $token,
                    'user' => $user
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar o token.',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function me()
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Usuário autenticado com sucesso.',
                'data' => auth('api')->user()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter o usuário autenticado.',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso.',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar logout.',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            $token = auth('api')->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Token renovado com sucesso.',
                'data' => [
                    'access_token' => $token,
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao renovar token.',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'message' => 'Token gerado com sucesso.',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ]
        ], 200);
    }
}
