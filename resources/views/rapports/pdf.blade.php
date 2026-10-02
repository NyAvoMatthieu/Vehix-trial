<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport d'activités</title>
    <style>
        @page { margin: 90px 35px 60px 35px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1f2937; }

        header { position: fixed; top: -70px; left: 0; right: 0; height: 60px; border-bottom: 2px solid #4f46e5; }
        header table { width: 100%; }
        header img { height: 48px; }
        .title { font-size: 16px; font-weight: bold; color: #1e1b4b; }
        .subtitle { font-size: 9px; color: #6b7280; margin-top: 2px; }
        .right { text-align: right; }

        footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 30px; border-top: 1px solid #d1d5db;
                 font-size: 8px; color: #6b7280; padding-top: 5px; }
        footer .pagenum:before { content: counter(page); }

        h2 { font-size: 12px; color: #1e1b4b; margin: 16px 0 6px; border-left: 4px solid #4f46e5; padding-left: 6px; }

        .kpis { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 0 -6px; }
        .kpi { background: #eef2ff; border: 1px solid #c7d2fe; padding: 8px; text-align: center; }
        .kpi .label { font-size: 8px; color: #6b7280; text-transform: uppercase; }
        .kpi .value { font-size: 15px; font-weight: bold; color: #3730a3; margin-top: 3px; }

        table.grid { width: 100%; border-collapse: collapse; }
        table.grid th { background: #4f46e5; color: #fff; text-align: left; padding: 5px 6px; font-size: 9px; }
        table.grid td { padding: 5px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        table.grid tr.alt td { background: #f9fafb; }
        .num { text-align: right; white-space: nowrap; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; }
        .b-trajet { background: #dbeafe; color: #1e40af; }
        .b-ravitaillement { background: #ffedd5; color: #9a3412; }
        .b-maintenance { background: #f3e8ff; color: #6b21a8; }
        .b-assurance { background: #dcfce7; color: #166534; }
        .b-visite { background: #e0e7ff; color: #3730a3; }

        .details { font-size: 8px; color: #4b5563; margin-top: 3px; }
        .details span { display: inline-block; margin-right: 8px; }
        .details b { color: #374151; }
        tr.activity { page-break-inside: avoid; }
        .empty { text-align: center; padding: 30px; color: #6b7280; }
    </style>
</head>
@php
    use Carbon\Carbon;

    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ') . ' Ar';
    $labels = [
        'trajet' => 'Trajet', 'ravitaillement' => 'Ravitaillement', 'maintenance' => 'Maintenance',
        'assurance' => 'Assurance', 'visite' => 'Visite technique',
    ];
    $typeFilterLabels = [
        'all' => 'Toutes', 'trajets' => 'Trajets', 'ravitaillements' => 'Ravitaillements',
        'maintenances' => 'Maintenances', 'assurances' => 'Assurances', 'visites' => 'Visites techniques',
    ];
    // dompdf ne sait pas afficher les emojis : on les retire des libellés
    $clean = fn ($s) => trim(preg_replace('/[\x{1F000}-\x{1FFFF}\x{2600}-\x{27BF}\x{FE0F}]/u', '', (string) $s));
@endphp
<body>
<header>
    <table>
        <tr>
            <td style="width: 60px;">
                @if($logo)<img src="{{ $logo }}" alt="Vehix">@endif
            </td>
            <td>
                <div class="title">Rapport d'activités</div>
                <div class="subtitle">
                    Période : {{ Carbon::parse($filters['start_date'])->format('d/m/Y') }}
                    au {{ Carbon::parse($filters['end_date'])->format('d/m/Y') }}
                </div>
            </td>
            <td class="right subtitle">
                Généré le {{ $generatedAt->format('d/m/Y à H:i') }}<br>
                par {{ $user->name }}
            </td>
        </tr>
    </table>
</header>

<footer>
    <table style="width:100%"><tr>
        <td>Vehix — Gestion de flotte</td>
        <td class="right">Page <span class="pagenum"></span></td>
    </tr></table>
</footer>

<main>
    <p class="subtitle">
        Véhicule :
        <b>{{ $selectedVehicule ? $selectedVehicule->full_name . ' (' . $selectedVehicule->license_plate . ')' : 'Tous les véhicules' }}</b>
        &nbsp;|&nbsp; Type d'activité : <b>{{ $typeFilterLabels[$filters['activity_type']] ?? 'Toutes' }}</b>
        @if(!empty($filters['search'])) &nbsp;|&nbsp; Recherche : <b>« {{ $filters['search'] }} »</b> @endif
    </p>

    <h2>Résumé</h2>
    <table class="kpis">
        <tr>
            <td class="kpi"><div class="label">Total activités</div><div class="value">{{ $stats['total_activities'] }}</div></td>
            <td class="kpi"><div class="label">Coût total</div><div class="value">{{ $fmt($stats['total_amount']) }}</div></td>
        </tr>
    </table>

    <h2>Répartition par type</h2>
    <table class="grid">
        <thead>
        <tr><th>Type</th><th class="num">Nombre</th><th class="num">Part</th><th class="num">Coût</th></tr>
        </thead>
        <tbody>
        @php
            $rows = [
                ['Trajets', 'trajets', null],
                ['Ravitaillements', 'ravitaillements', 'ravitaillements'],
                ['Maintenances', 'maintenances', 'maintenances'],
                ['Assurances', 'assurances', 'assurances'],
                ['Visites techniques', 'visites', 'visites'],
            ];
        @endphp
        @foreach($rows as $i => [$label, $countKey, $costKey])
            @php $count = $stats['by_type'][$countKey]; @endphp
            <tr class="{{ $i % 2 ? 'alt' : '' }}">
                <td>{{ $label }}</td>
                <td class="num">{{ $count }}</td>
                <td class="num">{{ $stats['total_activities'] > 0 ? round($count / $stats['total_activities'] * 100) : 0 }} %</td>
                <td class="num">{{ $costKey ? $fmt($stats['costs_by_type'][$costKey]) : '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Historique des activités ({{ $activities->count() }})</h2>

    @if($activities->isEmpty())
        <div class="empty">Aucune activité ne correspond aux critères sélectionnés.</div>
    @else
        <table class="grid">
            <thead>
            <tr>
                <th style="width: 58px;">Date</th>
                <th style="width: 70px;">Type</th>
                <th>Activité</th>
                <th style="width: 85px;">Véhicule</th>
                <th class="num" style="width: 70px;">Montant</th>
            </tr>
            </thead>
            <tbody>
            @foreach($activities as $i => $a)
                <tr class="activity {{ $i % 2 ? 'alt' : '' }}">
                    <td>{{ Carbon::parse($a['date'])->format('d/m/Y') }}</td>
                    <td><span class="badge b-{{ $a['type'] }}">{{ $labels[$a['type']] ?? $a['type'] }}</span></td>
                    <td>
                        <b>{{ $a['title'] }}</b><br>
                        <span style="color:#6b7280">{{ $a['description'] }}</span>
                        <div class="details">
                            @foreach($a['details'] as $key => $value)
                                <span><b>{{ $clean($key) }} :</b> {{ $value }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td>{{ $a['license_plate'] }}<br><span style="color:#6b7280">{{ $a['vehicule'] }}</span></td>
                    <td class="num">{{ $a['amount'] ? $fmt($a['amount']) : '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</main>
</body>
</html>