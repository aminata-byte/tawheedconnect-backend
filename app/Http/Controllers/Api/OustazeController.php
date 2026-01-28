<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Oustaze;
use App\Http\Resources\OustazeResource;
use Illuminate\Http\Request;

class OustazeController extends Controller
{
    /**
     * Liste tous les oustazes
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->isAssociation()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès réservé aux associations'
                ], 403);
            }

            $oustazes = Oustaze::where('association_id', $user->association->id)->get();

            return response()->json([
                'success' => true,
                'data' => OustazeResource::collection($oustazes)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur liste Oustazes: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des oustazes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouvel oustaze
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->isAssociation()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seules les associations peuvent créer des oustazes'
                ], 403);
            }

            $validated = $request->validate([
                'nom_complet' => 'required|string|max:255',
                'specialites' => 'required|string|max:255',
                'telephone' => 'required|string|max:20',
            ]);

            $validated['association_id'] = $user->association->id;

            $oustaze = Oustaze::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Oustaze créé avec succès',
                'data' => new OustazeResource($oustaze)
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur création Oustaze: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de la création',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un oustaze spécifique
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();

            if (!$user->isAssociation()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès réservé aux associations'
                ], 403);
            }

            $oustaze = Oustaze::where('association_id', $user->association->id)
                             ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new OustazeResource($oustaze)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Oustaze introuvable'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur affichage Oustaze: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un oustaze
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();

            if (!$user->isAssociation()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès réservé aux associations'
                ], 403);
            }

            $oustaze = Oustaze::where('association_id', $user->association->id)
                             ->findOrFail($id);

            $validated = $request->validate([
                'nom_complet' => 'sometimes|required|string|max:255',
                'specialites' => 'sometimes|required|string|max:255',
                'telephone' => 'sometimes|required|string|max:20',
            ]);

            $oustaze->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Oustaze mis à jour avec succès',
                'data' => new OustazeResource($oustaze->fresh())
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Oustaze introuvable'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour Oustaze: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un oustaze
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();

            if (!$user->isAssociation()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès réservé aux associations'
                ], 403);
            }

            $oustaze = Oustaze::where('association_id', $user->association->id)
                             ->findOrFail($id);

            $oustaze->delete();

            return response()->json([
                'success' => true,
                'message' => 'Oustaze supprimé avec succès'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Oustaze introuvable'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur suppression Oustaze: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}