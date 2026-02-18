<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            border: 2px solid black;
        }

        th, td {
            padding: 4px;
            text-align: left;
            /* Rimuoviamo i bordi tratteggiati generici */
            border: none;
        }

        thead th {
            border-top: 2px solid black;
            border-bottom: 2px solid black;
        }

        /* Bordo sinistro e destro della tabella su ogni cella esterna */
        tr td:first-child, tr th:first-child { border-left: 2px solid black; }
        tr td:last-child, tr th:last-child { border-right: 2px solid black; }

        /* Riga delle Note: aggiungiamo il tratteggio SOPRA */
        .note-row td {
            /* border-top: 1px dashed black; */
            font-style: italic; /* Opzionale: rende le note distinguibili */
            padding-bottom: 8px;
        }

        /* Separatore tra i blocchi di contatti: bordo solido sotto la riga note */
        .note-row td {
            border-bottom: 1px dashed black; /* Un separatore leggero tra un cliente e l'altro */
        }

        /* Ultima riga della tabella */
        tbody tr:last-child td {
            border-bottom: 2px solid black;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center">
        <u>
            @switch($resourceType)
                @case('calls')
                    Elenco Chiamate
                    @break
                @case('visits')
                    Elenco Visite
                    @break
                @case('deadlines')
                    Elenco Scadenze
                    @break
                @default
                    Elenco Contatti
            @endswitch
        </u>
    </h2>
    @if(!empty($filters) || $search)
        <p><strong>Filtri applicati:</strong></p>
        <ul>
            @if($search)
                <li>Ricerca: {{ $search }}</li>
            @endif
            @if(!empty($filters['region_id']['value']))
                <li>
                    Regione:
                    @php
                        $region = \App\Models\Region::where('id', $filters['region_id']['value'])->first();
                        $regionName = $region ? $region->name : $filters['region_id']['value'];
                    @endphp
                    {{ $regionName }}
                </li>
            @endif
            @if(!empty($filters['province_id']['value']))
                <li>
                    Provincia:
                    @php
                        $province = \App\Models\Province::where('id', $filters['province_id']['value'])->first();
                        $provinceName = $province ? $province->name : $filters['province_id']['value'];
                    @endphp
                    {{ $provinceName }}
                </li>
            @endif
            @if(!empty($filters['user_id']['value']))
                <li>
                    Utente:
                    @php
                        $user = \App\Models\User::where('id', $filters['user_id']['value'])->first();
                        $userName = $user ? $user->name : $filters['user_id']['value'];
                    @endphp
                    {{ $userName }}
                </li>
            @endif
            @if(!empty($filters['date_range']['from_date']) || !empty($filters['date_range']['to_date']))
                <li>
                    Intervallo date:
                    @php
                        $fromDate = !empty($filters['date_range']['from_date']) ? $filters['date_range']['from_date'] : '';
                        $toDate = !empty($filters['date_range']['to_date']) ? $filters['date_range']['to_date'] : '';
                        $dateRange = $fromDate && $toDate ? "Dal $fromDate al $toDate" : ($fromDate ? "Da $fromDate" : "Fino a $toDate");
                    @endphp
                    {{ $dateRange }}
                </li>
            @endif
        </ul>
    @endif
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Data</th>
                <th>Orario</th>
                <th>Esito</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                {{-- Riga principale: senza bordo inferiore --}}
                <tr>
                    <td><strong>{{ $item->client?->name }}</strong></td>
                    <td>{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d/m/Y') : '' }}</td>
                    <td>{{ $item->time ? \Carbon\Carbon::parse($item->time)->format('H:i') : '' }}</td>
                    <td>
                        @if($resourceType === 'calls' || $resourceType === 'visits')
                            {{ $item->outcome_type?->getLabel() ?? $item->outcome_type }}
                        @else
                            {{ $item->outcome_type ?? '-' }}
                        @endif
                    </td>
                </tr>
                {{-- Riga Note: con bordo superiore tratteggiato --}}
                <tr class="note-row">
                    <td colspan="4">
                        @if($item->note)
                            <span style="color: #666;">Note:</span> {{ $item->note }}
                        @else
                            <span style="color: #ccc;">Nessuna nota</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
