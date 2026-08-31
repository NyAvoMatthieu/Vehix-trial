<?php

namespace App\Http\Controllers;

use App\Models\Assurance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Enums\VehiculeStatus;

class AssuranceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $user = $request->user();
        $selectedVehiculeId = session('selected_vehicule_id');

        $query = Assurance::with(['vehicule', 'user']);

        if ($user->isClient()) {
            if ($selectedVehiculeId) {
                $query->where('vehicule_id', $selectedVehiculeId);
            } else {
                $query->whereHas('vehicule', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        $assurances = $query->orderBy('end_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Assurances/Index', [
            'assurances' => $assurances,
            'selectedVehicule' => $selectedVehiculeId ? Vehicule::find($selectedVehiculeId) : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Assurance $assurance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assurance $assurance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assurance $assurance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assurance $assurance)
    {
        //
    }
}
