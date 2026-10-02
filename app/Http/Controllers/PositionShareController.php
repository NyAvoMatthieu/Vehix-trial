<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PositionShareController extends Controller
{
    protected const TTL_HOURS = 2;

    protected function cacheKey(string $token): string
    {
        return "position_share:{$token}";
    }

    /**
     * Créer une session de partage (déclenché par l'utilisateur authentifié
     * qui remplit le formulaire de trajet, depuis TrajetMap.vue).
     */
    public function create(Request $request)
    {
        $token = Str::random(40);

        Cache::put($this->cacheKey($token), [
            'lat' => null,
            'lng' => null,
            'updated_at' => null,
            'started' => false,
        ], now()->addHours(self::TTL_HOURS));

        // $shareUrl = $request->getSchemeAndHttpHost() . '/partage-position/' . $token;
        $shareUrl = 'https://exes-haven-reckless.ngrok-free.dev' . '/partage-position/' . $token;
        return response()->json([
            'token' => $token,
            'share_url' => $shareUrl,
            'expires_in_hours' => self::TTL_HOURS,
        ]);
    }
    /**
     * Page mobile affichée au conducteur (aucune authentification requise :
     * le token fait office de clé d'accès temporaire).
     */
    public function show(string $token)
    {
        $data = Cache::get($this->cacheKey($token));

        if (!$data) {
            return Inertia::render('PositionShare/Show', [
                'token' => $token,
                'expired' => true,
            ]);
        }

        return Inertia::render('PositionShare/Show', [
            'token' => $token,
            'expired' => false,
        ]);
    }

    /**
     * Le téléphone du conducteur envoie sa position (appelé toutes les ~5s).
     */
    public function update(Request $request, string $token)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $key = $this->cacheKey($token);

        if (!Cache::has($key)) {
            return response()->json(['message' => 'Session de partage expirée ou invalide.'], 404);
        }

        Cache::put($key, [
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'updated_at' => now()->toIso8601String(),
            'started' => true,
        ], now()->addHours(self::TTL_HOURS));

        return response()->json(['success' => true]);
    }

    /**
     * Le formulaire (Trajets/Create) lit la dernière position connue (polling).
     */
    public function poll(string $token)
    {
        $data = Cache::get($this->cacheKey($token));

        if (!$data) {
            return response()->json(['expired' => true], 404);
        }

        return response()->json([
            'expired' => false,
            'started' => $data['started'],
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'updated_at' => $data['updated_at'],
        ]);
    }
}
