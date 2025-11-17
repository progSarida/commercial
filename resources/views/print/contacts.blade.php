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
            border-top: 1px dashed black;
            border-bottom: 1px dashed black;
            border-left: none;
            border-right: none;
            padding: 4px;
            text-align: left;
        }

        thead th {
            border-top: 2px solid black;
            border-bottom: 2px solid black;
        }

        tbody tr:last-child td {
            border-bottom: 2px solid black;
        }

        tr td:first-child,
        tr th:first-child {
            border-left: 2px solid black;
        }

        tr td:last-child,
        tr th:last-child {
            border-right: 2px solid black;
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
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->client?->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->time)->format('H:i') }}</td>
                    <td>
                        @if($resourceType === 'calls' || $resourceType === 'visits')
                            {{ \App\Enums\OutcomeType::tryFrom($item->outcome_type)?->getLabel() ?? $item->outcome_type }}
                        @else
                            {{ $item->outcome_type ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $item->note }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
