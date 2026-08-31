<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de récupération de mot de passe par données propriétaire
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient les paramètres de configuration pour le système
    | de récupération de mot de passe basé sur les données propriétaire.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Durée de validité du token (en minutes)
    |--------------------------------------------------------------------------
    |
    | Définit combien de temps un token de récupération reste valide.
    | Par défaut : 30 minutes
    |
    */
    'token_lifetime' => env('RECOVERY_TOKEN_LIFETIME', 30),

    /*
    |--------------------------------------------------------------------------
    | Nombre maximum de tentatives
    |--------------------------------------------------------------------------
    |
    | Nombre de tentatives autorisées avant le blocage du compte.
    | Par défaut : 3 tentatives
    |
    */
    'max_attempts' => env('RECOVERY_MAX_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Durée de blocage (en minutes)
    |--------------------------------------------------------------------------
    |
    | Durée pendant laquelle un utilisateur est bloqué après avoir atteint
    | le nombre maximum de tentatives.
    | Par défaut : 30 minutes
    |
    */
    'block_duration' => env('RECOVERY_BLOCK_DURATION', 30),

    /*
    |--------------------------------------------------------------------------
    | Nombre de questions requises
    |--------------------------------------------------------------------------
    |
    | Nombre de questions de vérification à poser à l'utilisateur.
    | Par défaut : 3 questions
    |
    */
    'required_questions' => env('RECOVERY_REQUIRED_QUESTIONS', 3),

    /*
    |--------------------------------------------------------------------------
    | Questions pour propriétaire personnel
    |--------------------------------------------------------------------------
    |
    | Liste des champs disponibles pour les questions de vérification
    | pour un propriétaire de type personnel.
    |
    */
    'personal_questions' => [
        'date_naissance' => [
            'label' => 'Quelle est votre date de naissance ?',
            'type' => 'date',
            'required' => true,
        ],
        'lieu_naissance' => [
            'label' => 'Quel est votre lieu de naissance ?',
            'type' => 'text',
            'required' => true,
        ],
        'numero_piece_identite' => [
            'label' => 'Quel est votre numéro de pièce d\'identité ?',
            'type' => 'text',
            'required' => true,
        ],
        'numero_permis' => [
            'label' => 'Quel est votre numéro de permis de conduire ?',
            'type' => 'text',
            'required' => false,
        ],
        'telephone_mobile' => [
            'label' => 'Quel est votre numéro de téléphone mobile ?',
            'type' => 'text',
            'required' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Questions pour propriétaire entreprise
    |--------------------------------------------------------------------------
    |
    | Liste des champs disponibles pour les questions de vérification
    | pour un propriétaire de type entreprise.
    |
    */
    'company_questions' => [
        'nif' => [
            'label' => 'Quel est votre NIF ?',
            'type' => 'text',
            'required' => true,
        ],
        'statistique' => [
            'label' => 'Quel est votre numéro STAT ?',
            'type' => 'text',
            'required' => false,
        ],
        'rcs' => [
            'label' => 'Quel est votre numéro RCS ?',
            'type' => 'text',
            'required' => false,
        ],
        'date_creation' => [
            'label' => 'Quelle est la date de création de votre entreprise ?',
            'type' => 'date',
            'required' => false,
        ],
        'representant_numero_piece' => [
            'label' => 'Quel est le numéro de pièce d\'identité du représentant légal ?',
            'type' => 'text',
            'required' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Activation de la journalisation
    |--------------------------------------------------------------------------
    |
    | Active ou désactive la journalisation des tentatives de récupération.
    | Utile pour le suivi et la sécurité.
    |
    */
    'enable_logging' => env('RECOVERY_ENABLE_LOGGING', true),

    /*
    |--------------------------------------------------------------------------
    | Notification par email
    |--------------------------------------------------------------------------
    |
    | Envoyer une notification par email après une récupération réussie.
    |
    */
    'send_notification' => env('RECOVERY_SEND_NOTIFICATION', false),

];