<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Carbon\Carbon;

class ProprietairePasswordRecoveryController extends Controller
{
    /**
     * Afficher le formulaire de demande de récupération
     */
    public function showEmailForm()
    {
        return Inertia::render('Auth/ProprietaireRecovery/EmailForm');
    }

    /**
     * Vérifier l'email et générer les questions
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Aucun compte trouvé avec cet email.'
            ]);
        }

        // Vérifier si l'utilisateur est bloqué
        if ($user->recovery_blocked_until && Carbon::parse($user->recovery_blocked_until)->isFuture()) {
            $minutes = Carbon::parse($user->recovery_blocked_until)->diffInMinutes(now());
            return back()->withErrors([
                'email' => "Trop de tentatives échouées. Réessayez dans {$minutes} minutes."
            ]);
        }

        // Vérifier si l'utilisateur a un propriétaire
        $proprietaire = $user->proprietaire;

        if (!$proprietaire) {
            return back()->withErrors([
                'email' => 'Aucune donnée de propriétaire trouvée. Veuillez contacter l\'administrateur.'
            ]);
        }

        // Générer un token de récupération
        $token = Str::random(64);
        $user->update([
            'recovery_token' => Hash::make($token),
            'recovery_token_expires_at' => now()->addMinutes(30),
        ]);

        // Générer les questions basées sur le type de propriétaire
        $questions = $this->generateQuestions($proprietaire);

        return redirect()->route('password.recovery.questions', ['token' => $token])
            ->with('questions', $questions);
    }

    /**
     * Afficher le formulaire de questions
     */
    public function showQuestionsForm(Request $request, $token)
    {
        // Vérifier si le token existe et est valide
        $user = User::whereNotNull('recovery_token')
            ->where('recovery_token_expires_at', '>', now())
            ->get()
            ->first(function ($user) use ($token) {
                return Hash::check($token, $user->recovery_token);
            });

        if (!$user) {
            return redirect()->route('password.recovery.email')
                ->withErrors(['token' => 'Le lien de récupération est invalide ou a expiré.']);
        }

        $proprietaire = $user->proprietaire;
        $questions = $this->generateQuestions($proprietaire);

        return Inertia::render('Auth/ProprietaireRecovery/QuestionsForm', [
            'token' => $token,
            'questions' => $questions,
            'proprietaireType' => $proprietaire->type,
        ]);
    }

    /**
     * Vérifier les réponses
     */
    public function verifyAnswers(Request $request, $token)
    {
        $request->validate([
            'answers' => ['required', 'array', 'min:3'],
        ]);

        // Récupérer l'utilisateur
        $user = User::whereNotNull('recovery_token')
            ->where('recovery_token_expires_at', '>', now())
            ->get()
            ->first(function ($user) use ($token) {
                return Hash::check($token, $user->recovery_token);
            });

        if (!$user) {
            return back()->withErrors([
                'token' => 'Le lien de récupération est invalide ou a expiré.'
            ]);
        }

        $proprietaire = $user->proprietaire;

        // Vérifier les réponses
        $correct = $this->validateAnswers($proprietaire, $request->answers);

        if (!$correct) {
            // Incrémenter les tentatives
            $user->increment('recovery_attempts');

            // Bloquer après 3 tentatives
            if ($user->recovery_attempts >= 3) {
                $user->update([
                    'recovery_blocked_until' => now()->addMinutes(30),
                    'recovery_attempts' => 0,
                    'recovery_token' => null,
                    'recovery_token_expires_at' => null,
                ]);

                return back()->withErrors([
                    'answers' => 'Trop de tentatives échouées. Vous êtes bloqué pendant 30 minutes.'
                ]);
            }

            return back()->withErrors([
                'answers' => 'Les réponses fournies sont incorrectes. Tentative ' . $user->recovery_attempts . '/3.'
            ]);
        }

        // Réponses correctes - afficher le formulaire de réinitialisation
        return redirect()->route('password.recovery.reset', ['token' => $token]);
    }

    /**
     * Afficher le formulaire de réinitialisation
     */
    public function showResetForm($token)
    {
        $user = User::whereNotNull('recovery_token')
            ->where('recovery_token_expires_at', '>', now())
            ->get()
            ->first(function ($user) use ($token) {
                return Hash::check($token, $user->recovery_token);
            });

        if (!$user) {
            return redirect()->route('password.recovery.email')
                ->withErrors(['token' => 'Le lien de récupération est invalide ou a expiré.']);
        }

        return Inertia::render('Auth/ProprietaireRecovery/ResetPassword', [
            'token' => $token,
            'email' => $user->email,
        ]);
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request, $token)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::whereNotNull('recovery_token')
            ->where('recovery_token_expires_at', '>', now())
            ->get()
            ->first(function ($user) use ($token) {
                return Hash::check($token, $user->recovery_token);
            });

        if (!$user) {
            return back()->withErrors([
                'token' => 'Le lien de récupération est invalide ou a expiré.'
            ]);
        }

        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($request->password),
            'recovery_token' => null,
            'recovery_token_expires_at' => null,
            'recovery_attempts' => 0,
            'recovery_blocked_until' => null,
        ]);

        return redirect()->route('login')
            ->with('status', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }

    /**
     * Générer les questions basées sur le type de propriétaire
     */
    private function generateQuestions(Proprietaire $proprietaire)
    {
        $questions = [];

        if ($proprietaire->type === 'personnel') {
            $questions = [
                [
                    'id' => 'date_naissance',
                    'question' => 'Quelle est votre date de naissance ?',
                    'type' => 'date',
                    'required' => true,
                ],
                [
                    'id' => 'lieu_naissance',
                    'question' => 'Quel est votre lieu de naissance ?',
                    'type' => 'text',
                    'required' => true,
                ],
                [
                    'id' => 'numero_piece_identite',
                    'question' => 'Quel est votre numéro de pièce d\'identité ?',
                    'type' => 'text',
                    'required' => true,
                ],
                [
                    'id' => 'numero_permis',
                    'question' => 'Quel est votre numéro de permis de conduire ?',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'id' => 'telephone_mobile',
                    'question' => 'Quel est votre numéro de téléphone mobile ?',
                    'type' => 'text',
                    'required' => true,
                ],
            ];
        } else {
            $questions = [
                [
                    'id' => 'nif',
                    'question' => 'Quel est votre NIF ?',
                    'type' => 'text',
                    'required' => true,
                ],
                [
                    'id' => 'statistique',
                    'question' => 'Quel est votre numéro STAT ?',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'id' => 'rcs',
                    'question' => 'Quel est votre numéro RCS ?',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'id' => 'date_creation',
                    'question' => 'Quelle est la date de création de votre entreprise ?',
                    'type' => 'date',
                    'required' => false,
                ],
                [
                    'id' => 'representant_numero_piece',
                    'question' => 'Quel est le numéro de pièce d\'identité du représentant légal ?',
                    'type' => 'text',
                    'required' => true,
                ],
            ];
        }

        // Filtrer pour ne garder que les questions dont les données existent
        $availableQuestions = collect($questions)->filter(function ($question) use ($proprietaire) {
            return !empty($proprietaire->{$question['id']});
        })->values();

        // Vérifier qu'on a au moins 3 questions disponibles
        if ($availableQuestions->count() < 3) {
            throw new \Exception(
                'Pas assez de données propriétaire pour générer des questions de sécurité. ' .
                'Veuillez contacter l\'administrateur.'
            );
        }

        return $availableQuestions->take(3)->toArray();
    }

    /**
     * Valider les réponses
     */
    private function validateAnswers(Proprietaire $proprietaire, array $answers)
    {
        $correctAnswers = 0;
        $totalQuestions = count($answers);

        foreach ($answers as $questionId => $answer) {
            $expectedValue = $proprietaire->{$questionId};

            if (empty($expectedValue)) {
                continue;
            }

            // Normaliser les réponses
        $normalizedAnswer = $this->normalizeAnswer($answer);
        $normalizedExpected = $this->normalizeAnswer($expectedValue);

        // Comparer
        if ($normalizedAnswer === $normalizedExpected) {
            $correctAnswers++;
        }
        }

        // Au moins 3 réponses correctes sur 3 questions
        return $correctAnswers === $totalQuestions && $totalQuestions >= 3;
    }

    /**
     * Normaliser une réponse pour la comparaison
     */
    private function normalizeAnswer($value)
    {
        if ($value instanceof \DateTime || $value instanceof Carbon) {
            return $value->format('Y-m-d');
        }
        // date (YYYY-MM-DD ou DD/MM/YYYY)
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }
        // Pour les autres valeurs (texte, numéros)
        return strtolower(trim(str_replace([' ', '-', '.', '/', '(', ')'], '', (string) $value)));
    }
}
