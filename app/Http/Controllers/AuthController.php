<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /** REGISTER ---------------------------------------------------------- */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, &$user) {
            // 1. Simpan user umum
            $user = User::create([
                'email'    => $data['email'],
                'name'     => $data['name'],
                'password' => Hash::make($data['password']),
                'role'     => $data['role'],
            ]);

            // 2. Simpan detail sesuai role
            match ($user->role) {
                'customer' => $user->customer()->create(['phone'=> $data['phone']]),
                'staff'    => $user->staff()->create(['specific_role'=> $data['specific_role']]),
                'manager'  => $user->manager()->create(['specialization'=> $data['specialization']]),
                'owner'    => $user->owner()->create(['level' => $data['level']]),
            };
        });

        // 3. Buat token Sanctum
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Register success',
            'data'    => [
                'user'  => $user->load($user->role), // auto eager-load detail
                'token' => $token,
            ],
        ], 201);
    }

    /** LOGIN ------------------------------------------------------------- */
    public function login(LoginRequest $request)
    {
        $creds = $request->only('email', 'password');

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $creds['email'])->first();

        if (! $user || ! Hash::check($creds['password'], $user->password)) {
            return response()->json(['message' => 'Credentials mismatch'], 422);
        }

        // single-device? => $user->tokens()->delete();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'data'    => [
                'user'  => $user->load($user->role),
                'token' => $token,
            ],
        ]);
    }

    /** LUPA PASSWORD ----------------------------------------------------- */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            ['email' => $request->validated()['email']]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 422);
    }
}
