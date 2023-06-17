<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate_data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'register' => ['required', 'numeric'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['numeric'],
            'password' => ['required', 'confirmed'],
            'ci' => ['numeric'],
            'is_student' => ['required', 'boolean'],
            'is_driver' => ['required', 'boolean'],
        ]);

        // Encriptar password
        $validate_data['password'] = Hash::make($request->password);

        $user = User::create($validate_data);

        $access_token = $user->createToken('auth_token')->plainTextToken;

        if ($user->is_driver) {
            Driver::create([
                'user_id' => $user->id,
                'ci' => $request->ci,
            ]);
        }

        return response([
            'user' => $user,
            'access_token' => $access_token,
        ], 201);
    }

    public function login(Request $request)
    {
        $login_data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if (!auth()->attempt($login_data)) {
            $validator = FacadesValidator::make($login_data, [
                'email' => 'exists:users,email',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $errorMessage = $errors->first();

                return response()->json([
                    'message' => 'Validation Failed',
                    'errors' => [
                        'authentication' => [$errorMessage],
                    ],
                ], 422);
            }

            return response()->json([
                'message' => 'Credenciales Inválidas',
                'errors' => [
                    'authentication' => 'Credenciales Inválidas',
                ],
            ], 401);
        }

        $access_token = auth()->user()->createToken('login_token')->plainTextToken;

        return response()->json([
            'user' => auth()->user(),
            'access_token' => $access_token
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();  // Elimina token

        return [
            'message' => 'Sección cerrada exitosamente'
        ];
    }
}
