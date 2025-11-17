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
            border-left: none;
            border-right: none;
            padding: 4px;
            text-align: left;
        }

        thead th {
            border-top: 2px solid black;
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

        /* Solid border only after the second row of each estimate */
        tr.estimate-second-row {
            border-bottom: 2px solid black;
        }

        /* No border between first and second row of the same estimate */
        tr.estimate-first-row {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center"><u>Elenco Preventivi</u></h2>
    @if(!empty($filters) || $search)
        <p><strong>Filtri applicati:</strong></p>
        <ul>
            @if($search)
                <li>Ricerca: {{ $search }}</li>
            @endif
            @if(!empty($filters['file_status']['value']))
                <li>
                    Stato documento: 
                    @php
                        $fileStatus = $filters['file_status']['value'] === 'uploaded' ? 'Caricato' : 'Richiesto';
                    @endphp
                    {{ $fileStatus }}
                </li>
            @endif
            @if(!empty($filters['estimate_state']['values']))
                <li>
                    Stato preventivo:
                    @php
                        $estimateStateValues = array_map(function ($value) {
                            return \App\Enums\EstimateState::tryFrom($value)?->getLabel() ?? $value;
                        }, $filters['estimate_state']['values']);
                    @endphp
                    {{ implode(', ', $estimateStateValues) }}
                </li>
            @endif
            @if(!empty($filters['date_range']['from_date']) || !empty($filters['date_range']['to_date']))
                <li>
                    Intervallo date:
                    @php
                        $fromDate = $filters['date_range']['from_date'] ?? 'N/D';
                        $toDate = $filters['date_range']['to_date'] ?? 'N/D';
                        $dateRange = $fromDate !== 'N/D' && $toDate !== 'N/D' ? "dal $fromDate al $toDate" : ($fromDate !== 'N/D' ? "dal $fromDate" : "al $toDate");
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
                <th>Tipo Contatto</th>
                <th>Stato</th>
                <th>Chiuso</th>
                <th>Richiesto da</th>
                <th>Chiuso da</th>
                <th>Stato Modificato da</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estimates as $estimate)
                <tr class="estimate-first-row">
                    <td>{{ $estimate->client->name ?? 'N/D' }}</td>
                    <td>{{ \Carbon\Carbon::parse($estimate->date)->format('d/m/Y') }}</td>
                    <td>{{ $estimate->contact_type?->getLabel() ?? 'N/D' }}</td>
                    <td>{{ $estimate->estimate_state?->getLabel() ?? 'N/D' }}</td>
                    <td>{{ $estimate->done ? 'Sì' : 'No' }}</td>
                    <td>{{ $estimate->userRequest?->name ?? 'N/D' }}</td>
                    <td>{{ $estimate->userDone?->name ?? 'N/D' }}</td>
                    <td>{{ $estimate->userState?->name ?? 'N/D' }}</td>
                    <td>{{ $estimate->path ? basename($estimate->path) : 'Nessun file' }}</td>
                </tr>
                <tr class="estimate-second-row">
                    <td colspan="9">
                        <strong>Servizi:</strong>
                        @php
                            $services = $estimate->getFormattedPrintClientServices();
                            echo nl2br(e($services));
                        @endphp
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>