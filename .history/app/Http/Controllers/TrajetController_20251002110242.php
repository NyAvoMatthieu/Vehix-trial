<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use Illuminate\Http\Request;
use App\Models\Vehicule;
use Inertia\Inertia;


class TrajetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $trajets = Trajet::with(['vehicule', 'user'])
            ->where('user_id', auth()->user()->id)
            ->orderBy('trajet_date', 'desc')
            ->orderBy('heure_depart', 'desc')
            ->paginate(10);

        return Inertia::render('Trajets/Index', [
            'trajets' => $trajets,
        ]);
    }
     public f

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $vehicules = Vehicule::where('user_id', auth()->id())
            ->where('validation_status', 'validated')
            ->get(['id', 'marque', 'modele', 'immatriculation']);

        return Inertia::render('Trajets/Create', [
            'vehicules' => $vehicules,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Trajet $trajet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trajet $trajet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trajet $trajet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trajet $trajet)
    {
        //
    }
}
