<?php

namespace App\Http\Controllers;

use App\Models\Proprietaire;
use App\Http\Requests\ProprietaireRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProprietaireController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Proprietaire::class);

        $user = $request->user();
        $query = Proprietaire::with('user');

        // Les clients ne voient que leur propriétaire
        if ($user->isClient()) {
            $query->where('user_id', $user->id);
        }

        // Filtres de recherche
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('raison_sociale', 'like', "%{$search}%")
                  ->orWhere('nom_commercial', 'like', "%{$search}%")
                  ->orWhere('nif', 'like', "%{$search}%")
                  ->orWhere('numero_piece_identite', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->get('type') !== 'all') {
            $query->where('type', $request->get('type'));
        }

        $proprietaires = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Proprietaires/Index', [
            'proprietaires' => $proprietaires,
            'filters' => $request->only(['search', 'type']),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Proprietaire::class);

          $redirectToVehicule = $request->query('redirect_to_vehicule', false);

    return Inertia::render('Proprietaires/Create', [
        'redirectToVehicule' => filter_var($redirectToVehicule, FILTER_VALIDATE_BOOLEAN),
    ]);
    }

    public function store(ProprietaireRequest $request)
    {
        $this->authorize('create', Proprietaire::class);

        $proprietaire = Proprietaire::create([
            'user_id' => $request->user()->id,
            ...$request->validated(),
        ]);

        return redirect()->route('proprietaires.show', $proprietaire)
            ->with('message', 'Propriétaire créé avec succès.');
    }

    public function show(Proprietaire $proprietaire)
    {
        $this->authorize('view', $proprietaire);

        $proprietaire->load('user');

        return Inertia::render('Proprietaires/Show', [
            'proprietaire' => $proprietaire,
        ]);
    }

    public function edit(Proprietaire $proprietaire)
    {
        $this->authorize('update', $proprietaire);

        return Inertia::render('Proprietaires/Edit', [
            'proprietaire' => $proprietaire,
        ]);
    }

    public function update(ProprietaireRequest $request, Proprietaire $proprietaire)
    {
        $this->authorize('update', $proprietaire);

        $proprietaire->update($request->validated());

        return redirect()->route('proprietaires.show', $proprietaire)
            ->with('message', 'Propriétaire mis à jour avec succès.');
    }

    public function destroy(Proprietaire $proprietaire)
    {
        $this->authorize('delete', $proprietaire);

        $proprietaire->delete();

        return redirect()->route('proprietaires.index')
            ->with('message', 'Propriétaire supprimé avec succès.');
    }
}
