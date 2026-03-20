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

        thead tr.top {
            border-top: 2px solid black;
        }

        thead tr.bottom {
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

        /* Solid border only after the third row of each bidding */
        tr.bidding-third-row {
            border-bottom: 2px dashed black;
        }

        /* No border for first and second rows of the same bidding */
        tr.bidding-first-row,
        tr.bidding-second-row {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center"><u>Elenco Gare</u></h2>
    @if(!empty($filters) || $search)
        <p><strong>Filtri applicati:</strong></p>
        <ul>
            @if($search)
                <li>Ricerca: {{ $search }}</li>
            @endif
            @if(!empty($filters['bidding_filter']['values']))
                <li>
                    Filtri rapidi:
                    @php
                        $biddingFilterValues = array_map(function ($value) {
                            return \App\Enums\BiddingFilter::tryFrom($value)?->getLabel() ?? $value;
                        }, $filters['bidding_filter']['values']);
                    @endphp
                    {{ implode(', ', $biddingFilterValues) }}
                </li>
            @endif
            @if(!empty($filters['past_deadline']['show_past_deadline']))
                <li>Incluse scadute</li>
            @endif
            @if(!empty($filters['inspection_date_range']['inspection_from_date']) || !empty($filters['inspection_date_range']['inspection_to_date']))
                <li>
                    Sopralluoghi:
                    @php
                        $fromDate = $filters['inspection_date_range']['inspection_from_date'] ?? 'N/D';
                        $toDate = $filters['inspection_date_range']['inspection_to_date'] ?? 'N/D';
                        $dateRange = $fromDate !== 'N/D' && $toDate !== 'N/D' ? "dal $fromDate al $toDate" : ($fromDate !== 'N/D' ? "dal $fromDate" : "al $toDate");
                    @endphp
                    {{ $dateRange }}
                </li>
            @endif
            @if(!empty($filters['deadline_date_range']['deadline_from_date']) || !empty($filters['deadline_date_range']['deadline_to_date']))
                <li>
                    Scadenze:
                    @php
                        $fromDate = $filters['deadline_date_range']['deadline_from_date'] ?? 'N/D';
                        $toDate = $filters['deadline_date_range']['deadline_to_date'] ?? 'N/D';
                        $dateRange = $fromDate !== 'N/D' && $toDate !== 'N/D' ? "dal $fromDate al $toDate" : ($fromDate !== 'N/D' ? "dal $fromDate" : "al $toDate");
                    @endphp
                    {{ $dateRange }}
                </li>
            @endif
            @if(!empty($filters['services']['values']))
                <li>
                    Servizi:
                    @php
                        $serviceValues = array_map(function ($value) {
                            return \App\Models\ServiceType::find($value)?->name ?? $value;
                        }, $filters['services']['values']);
                    @endphp
                    {{ implode(', ', $serviceValues) }}
                </li>
            @endif
            @if(!empty($filters['bidding_type_id']['values']))
                <li>
                    Tipi gara:
                    @php
                        $biddingTypeValues = array_map(function ($value) {
                            return \App\Models\BiddingType::find($value)?->name ?? $value;
                        }, $filters['bidding_type_id']['values']);
                    @endphp
                    {{ implode(', ', $biddingTypeValues) }}
                </li>
            @endif
            @if(!empty($filters['feasibility_type']['values']))
                <li>
                    Fattibilità:
                    @php
                        $feasibilityTypeValues = array_map(function ($value) {
                            return \App\Enums\FeasibilityType::tryFrom($value)?->getLabel() ?? $value;
                        }, $filters['feasibility_type']['values']);
                    @endphp
                    {{ implode(', ', $feasibilityTypeValues) }}
                </li>
            @endif
            @if(!empty($filters['bidding_state_id']['values']))
                <li>
                    Stati gara:
                    @php
                        $biddingStateValues = array_map(function ($value) {
                            return \App\Models\BiddingState::find($value)?->name ?? $value;
                        }, $filters['bidding_state_id']['values']);
                    @endphp
                    {{ implode(', ', $biddingStateValues) }}
                </li>
            @endif
            @if(!empty($filters['bidding_processing_state']['values']))
                <li>
                    Stati lavorazione:
                    @php
                        $processingStateValues = array_map(function ($value) {
                            return \App\Enums\BiddingProcessingState::tryFrom($value)?->getLabel() ?? $value;
                        }, $filters['bidding_processing_state']['values']);
                    @endphp
                    {{ implode(', ', $processingStateValues) }}
                </li>
            @endif
        </ul>
    @endif
    <table>
        <thead>
            <tr class="top">
                <th>Ente</th>
                <th>Prov.</th>
                <th>Scadenza</th>
                <th>Sopralluogo</th>
                <th>Chiarimenti</th>
                <th>Tipo gara</th>
            </tr>
            <tr class="bottom">
                <th>Fattibilità</th>
                <th>Dettaglio fattibilità</th>
                <th>Importo</th>
                <th>Stato lavorazione</th>
                <th>Priorità</th>
                <th>Procedura</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biddings as $bidding)
                <tr class="bidding-first-row">
                    <td colspan="6">
                        <strong>Descrizione:</strong> {{ Str::limit($bidding->description, 120, '...') ?: 'N/D' }}
                    </td>
                </tr>
                <tr class="bidding-second-row">
                    <td>{{ $bidding->client?->name ?? '...' }}</td>
                    <td>{{ $bidding->province?->name ?? '...' }}</td>
                    <td>{{ $bidding->deadline_date ? \Carbon\Carbon::parse($bidding->deadline_date)->format('d/m/Y') : \Carbon\Carbon::parse($bidding->interest_deadline_date)->format('d/m/Y') }}</td>
                    <td>{{ $bidding->inspection_deadline_date ? \Carbon\Carbon::parse($bidding->inspection_deadline_date)->format('d/m/Y') : '...' }}</td>
                    <td>{{ $bidding->clarification_request_deadline_date ? \Carbon\Carbon::parse($bidding->clarification_request_deadline_date)->format('d/m/Y') : '...' }}</td>
                    <td>{{ $bidding->biddingType?->name ?? $bidding->interest_expression_type?->getLabel() }}</td>
                </tr>
                <tr>
                    <td>{{ $bidding->feasibility_type?->getLabel() ?? '...' }}</td>
                    <td>{{ $bidding->biddingState?->name ?? '...' }}</td>
                    <td>{{ $bidding->amount ? '€ ' . number_format($bidding->amount, 2, ',', '.') : '...' }}</td>
                    <td>{{ $bidding->bidding_processing_state?->getLabel() ?? '...' }}</td>
                    <td>{{ $bidding->bidding_priority_type?->getLabel() ?? '...' }}</td>
                    <td>{{ $bidding->bidding_procedure_type?->getLabel() ?? '...' }}</td>
                </tr>
                <tr class="bidding-third-row">
                    <td colspan="6">
                        <strong>Servizi:</strong> {{ $bidding->serviceTypes->pluck('name')->join(' - ') ?: '...' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
