<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Liste tous les événements de l'association
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

            $events = Event::where('association_id', $user->association->id)
                          ->orderBy('start_date', 'desc')
                          ->get();

            return response()->json([
                'success' => true,
                'data' => EventResource::collection($events)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur liste événements: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des événements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouvel événement
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->isAssociation()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seules les associations peuvent créer des événements'
                ], 403);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'required|string|max:255',
                'city' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|string',
                'end_time' => 'nullable|string',
                'type' => 'nullable|string|in:event,conference,workshop,seminar,webinar',
                'category' => 'nullable|string',
                'max_participants' => 'nullable|integer|min:1',
                'requires_registration' => 'nullable|boolean',
                'speakers' => 'nullable|string',
                'tags' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Construire la date et heure de début
            $startDate = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
            
            // Construire la date et heure de fin si fournie
            $endDate = null;
            if (!empty($validated['end_time'])) {
                $endDate = Carbon::parse($validated['date'] . ' ' . $validated['end_time']);
            }

            // Gérer l'upload de l'image
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
            }

            // Préparer les organisateurs (speakers)
            $organizers = null;
            if (!empty($validated['speakers'])) {
                $organizers = array_map('trim', explode(',', $validated['speakers']));
            }

            // Préparer les tags
            $tags = null;
            if (!empty($validated['tags'])) {
                $tags = array_map('trim', explode(',', $validated['tags']));
            }

            $event = Event::create([
                'association_id' => $user->association->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'location' => $validated['location'],
                'city' => $validated['city'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type' => $validated['type'] ?? 'event',
                'category' => $validated['category'] ?? null,
                'max_participants' => $validated['max_participants'] ?? null,
                'requires_registration' => $validated['requires_registration'] ?? false,
                'status' => 'upcoming',
                'organizers' => $organizers,
                'tags' => $tags,
                'image' => $imagePath,
                'participants_count' => 0,
                'views_count' => 0,
                'shares_count' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Événement créé avec succès',
                'data' => new EventResource($event)
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur création événement: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de la création',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un événement spécifique
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

            $event = Event::where('association_id', $user->association->id)
                         ->findOrFail($id);

            // Incrémenter le compteur de vues
            $event->increment('views_count');

            return response()->json([
                'success' => true,
                'data' => new EventResource($event)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Événement introuvable'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur affichage événement: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un événement
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

            $event = Event::where('association_id', $user->association->id)
                         ->findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'sometimes|required|string|max:255',
                'city' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'date' => 'sometimes|required|date',
                'start_time' => 'sometimes|required|string',
                'end_time' => 'nullable|string',
                'type' => 'nullable|string|in:event,conference,workshop,seminar,webinar',
                'category' => 'nullable|string',
                'max_participants' => 'nullable|integer|min:1',
                'requires_registration' => 'nullable|boolean',
                'status' => 'nullable|in:draft,upcoming,ongoing,finished,cancelled',
                'speakers' => 'nullable|string',
                'tags' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Mettre à jour les dates si fournies
            if (isset($validated['date']) && isset($validated['start_time'])) {
                $event->start_date = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
            }

            if (isset($validated['date']) && isset($validated['end_time'])) {
                $event->end_date = Carbon::parse($validated['date'] . ' ' . $validated['end_time']);
            }

            // Gérer l'upload de la nouvelle image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                if ($event->image) {
                    Storage::disk('public')->delete($event->image);
                }
                $event->image = $request->file('image')->store('events', 'public');
            }

            // Mettre à jour les organisateurs
            if (isset($validated['speakers'])) {
                $event->organizers = array_map('trim', explode(',', $validated['speakers']));
            }

            // Mettre à jour les tags
            if (isset($validated['tags'])) {
                $event->tags = array_map('trim', explode(',', $validated['tags']));
            }

            // Mettre à jour les autres champs
            $event->fill(array_filter($validated, function($key) {
                return !in_array($key, ['date', 'start_time', 'end_time', 'speakers', 'tags', 'image']);
            }, ARRAY_FILTER_USE_KEY));

            $event->save();

            return response()->json([
                'success' => true,
                'message' => 'Événement mis à jour avec succès',
                'data' => new EventResource($event->fresh())
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Événement introuvable'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour événement: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un événement
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

            $event = Event::where('association_id', $user->association->id)
                         ->findOrFail($id);

            // Supprimer l'image si elle existe
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }

            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Événement supprimé avec succès'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Événement introuvable'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur suppression événement: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}