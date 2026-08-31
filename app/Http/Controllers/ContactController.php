<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ], [
            'name.required' => 'Le nom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être valide',
            'subject.required' => 'Le sujet est obligatoire',
            'message.required' => 'Le message est obligatoire',
            'message.min' => 'Le message doit contenir au moins 10 caractères',
        ]);

        try {
            // Envoi de l'email
            Mail::to('hasinaandritina538@gmail.com')->send(new ContactFormMail($validated));

            // Supporter à la fois JSON et Inertia
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.'
                ]);
            }

            return back()->with('success', 'Votre message a été envoyé avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur envoi email: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer.'
                ], 500);
            }

            return back()->withErrors(['message' => 'Une erreur est survenue lors de l\'envoi du message.']);
        }
    }
}
