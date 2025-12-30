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
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur (par numéro de téléphone)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:member,association',
            'email' => 'nullable|email|unique:users,email',
            'city' => 'nullable|string',

            // Pour association
            'association_name' => 'required_if:role,association|string|max:255',
            'association_description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ]);

        // Générer un code de vérification
        $verificationCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        Log::info("VERIFICATION CODE INSCRIPTION for phone {$validated['phone']}: {$verificationCode}");

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email' => $validated['email'] ?? null,
            'city' => $validated['city'] ?? null,
            'verification_code' => $verificationCode,
        ]);

        // Si c'est une association, créer le profil association
        if ($validated['role'] === 'association') {
            $logoPath = null;

            // Upload logo si présent
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = 'logo_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $logoPath = $file->storeAs('logos', $filename, 'public');

                Log::info("Logo uploadé pour association: {$logoPath}");
            }

            // Créer l'association
            $association = Association::create([
                'user_id' => $user->id,
                'name' => $request->association_name,
                'description' => $request->association_description ?? null,
                'logo' => $logoPath,
            ]);

            // Ajouter l'association à l'utilisateur pour la réponse
            $user->association = $association;

            Log::info("Association créée avec ID: {$association->id}");
        }

        // TODO: Envoyer SMS avec le code (plus tard)
        // SMS::send($user->phone, "Votre code TawheedConnect: {$verificationCode}");

        // Créer le token Sanctum
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie',
            'data' => [
                'user' => $user,
                'token' => $token,
                'verification_code' => $verificationCode, // À retirer en production
            ]
        ], 201);
    }

    /**
     * Connexion par numéro de téléphone
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Numéro de téléphone ou mot de passe incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte est désactivé.',
            ], 403);
        }

        // Si c'est une association, charger les infos association
        if ($user->role === 'association') {
            $user->load('association');
        }

        // Supprimer les anciens tokens
        $user->tokens()->delete();

        // Créer un nouveau token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ]);
    }

    /**
     * Vérifier le numéro de téléphone avec le code SMS
     */
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)
            ->where('verification_code', $request->code)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Code de vérification invalide.',
            ], 400);
        }

        $user->update([
            'phone_verified' => true,
            'verification_code' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Numéro vérifié avec succès',
            'data' => ['user' => $user]
        ]);
    }

    /**
     * Renvoyer le code de vérification
     */
    public function resendCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur introuvable.',
            ], 404);
        }

        $verificationCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        Log::info("VERIFICATION CODE RENVOYÉ for phone {$request->phone}: {$verificationCode}");

        $user->update([
            'verification_code' => $verificationCode,
        ]);

        // TODO: Envoyer vrai SMS plus tard
        return response()->json([
            'success' => true,
            'message' => 'Code renvoyé avec succès',
            'data' => [
                'verification_code' => $verificationCode // À retirer en prod
            ]
        ]);
    }

    /**
     * Déconnexion (supprimer le token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Obtenir l'utilisateur connecté
     */
    public function me(Request $request)
    {
        $user = $request->user();

        // Si c'est une association, charger les infos association
        if ($user->role === 'association') {
            $user->load('association');
        }

        return response()->json([
            'success' => true,
            'data' => ['user' => $user]
        ]);
    }

    /**
     * Mot de passe oublié
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Numéro de téléphone introuvable.',
            ], 404);
        }

        $resetCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        Log::info("RESET CODE for phone {$request->phone}: {$resetCode}");

        $user->update([
            'verification_code' => $resetCode,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Code de réinitialisation envoyé',
            'data' => [
                'reset_code' => $resetCode // À retirer en prod
            ]
        ]);
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('phone', $request->phone)
            ->where('verification_code', $request->code)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Code invalide.',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'verification_code' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès'
        ]);
    }
}
