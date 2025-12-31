<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Utilitaire privé pour normaliser le numéro de téléphone
     */
    private function formatPhone($phone)
    {
        return str_replace(['+', ' '], '', $phone);
    }

    /**
     * INSCRIPTION
     */
    public function register(Request $request)
    {
        // On nettoie le téléphone avant la validation unique
        if ($request->has('phone')) {
            $request->merge(['phone' => $this->formatPhone($request->phone)]);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'required|string|unique:users,phone',
            'password'   => 'required|string|min:6|confirmed',
            'role'       => 'required|in:member,association',
            'email'      => 'nullable|email|unique:users,email',
            'city'       => 'nullable|string',
            'association_name'        => 'required_if:role,association|string|max:255',
            'association_description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $verificationCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        Log::info("VERIFICATION CODE REGISTER {$validated['phone']} : {$verificationCode}");

        $user = User::create([
            'first_name'        => $validated['first_name'],
            'last_name'         => $validated['last_name'],
            'phone'             => $validated['phone'],
            'password'          => Hash::make($validated['password']),
            'role'              => $validated['role'],
            'email'             => $validated['email'] ?? null,
            'city'              => $validated['city'] ?? null,
            'verification_code' => $verificationCode,
            'is_active'         => true,
        ]);

        if ($validated['role'] === 'association') {
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = 'logo_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $logoPath = $file->storeAs('logos', $filename, 'public');
            }

            Association::create([
                'user_id'     => $user->id,
                'name'        => $validated['association_name'],
                'description' => $validated['association_description'] ?? null,
                'logo'        => $logoPath,
            ]);

            $user->load('association');
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie',
            'data' => ['user' => $user],
            'token' => $token,
            'verification_code' => $verificationCode,
        ], 201);
    }

    /**
     * CONNEXION
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        $phone = $this->formatPhone($request->phone);
        $user = User::where('phone', $phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Numéro de téléphone ou mot de passe incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json(['success' => false, 'message' => 'Compte désactivé.'], 403);
        }

        if ($user->role === 'association') {
            $user->load('association');
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => ['user' => $user],
            'token' => $token,
        ]);
    }

    /**
     * UTILISATEUR CONNECTÉ
     */
    public function me(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'association') {
            $user->load('association');
        }

        return response()->json([
            'success' => true,
            'data' => ['user' => $user],
        ]);
    }

    /**
     * DÉCONNEXION
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Déconnexion réussie']);
    }

    /**
     * VÉRIFICATION TÉLÉPHONE
     */
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string',
        ]);

        $phone = $this->formatPhone($request->phone);
        $user = User::where('phone', $phone)
            ->where('verification_code', $request->code)
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Code invalide.'], 400);
        }

        $user->update([
            'phone_verified' => true,
            'verification_code' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Téléphone vérifié']);
    }

    /**
     * MOT DE PASSE OUBLIÉ
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $this->formatPhone($request->phone);
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
        }

        $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        Log::info("RESET CODE {$phone} : {$code}");

        $user->update(['verification_code' => $code]);

        return response()->json([
            'success' => true,
            'message' => 'Code envoyé',
            'reset_code' => $code,
        ]);
    }

    /**
     * RESET MOT DE PASSE
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'code'     => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $phone = $this->formatPhone($request->phone);
        $user = User::where('phone', $phone)
            ->where('verification_code', $request->code)
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Code invalide.'], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'verification_code' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé']);
    }
}