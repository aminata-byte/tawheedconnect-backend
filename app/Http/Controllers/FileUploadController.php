<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Upload logo pour association
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ]);

        try {
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');

                // Générer nom unique
                $filename = 'logo_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                // Stocker dans storage/app/public/logos
                $path = $file->storeAs('logos', $filename, 'public');

                // URL complète
                $url = Storage::url($path);

                return response()->json([
                    'success' => true,
                    'message' => 'Logo uploadé avec succès',
                    'path' => $path,
                    'url' => url($url),
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Aucun fichier reçu'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload photo profil
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:3072', // Max 3MB
        ]);

        try {
            $user = auth()->user();

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');

                // Supprimer ancienne photo si existe
                if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }

                // Générer nom unique
                $filename = 'photo_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Stocker dans storage/app/public/photos
                $path = $file->storeAs('photos', $filename, 'public');

                // Mettre à jour user
                $user->photo = $path;
                $user->save();

                // URL complète
                $url = Storage::url($path);

                return response()->json([
                    'success' => true,
                    'message' => 'Photo uploadée avec succès',
                    'path' => $path,
                    'url' => url($url),
                    'user' => $user
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Aucun fichier reçu'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur upload: ' . $e->getMessage()
            ], 500);
        }
    }
}
