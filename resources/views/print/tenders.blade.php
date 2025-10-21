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

        /* Dashed border only after the third row of each tender */
        tr.tender-third-row {
            border-bottom: 2px dashed black;
        }

        /* No border for first and second rows of the same tender */
        tr.tender-first-row,
        tr.tender-second-row {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center"><u>Elenco Appalti</u></h2>
    @if(!empty($filters) || $search)
        <p><strong>Filtri applicati:</strong></p>
        <ul>
            @if($search)
                <li>Ricerca: {{ $search }}</li>
            @endif
            @if(!empty($filters['bidding_id']['value']))
                <li>
                    Gara:
                    @php
                        $bidding = \App\Models\Bidding::find($filters['bidding_id']['value']);
                    @endphp
                    {{ $bidding->client->name . " - " . $bidding?->description ?? 'N/D' }}
                </li>
            @endif
            @if(!empty($filters['open_procedure_check']['value']))
                <li>
                    {{ $filters['open_procedure_check']['value'] === 'open' ? 'Procedure aperte' : 'Procedure chiuse' }}
                </li>
            @endif
            @if(!empty($filters['invitation_request_check']['show_invitation_request']))
                <li>Richiesta invito</li>
            @endif
            @if(!empty($filters['partnership_require_check']['show_partnership_require']))
                <li>ATI necessaria</li>
            @endif
            @if(!empty($filters['collection_require_check']['show_collection_require']))
                <li>Richiesta incassi</li>
            @endif
            @if(!empty($filters['reliance_require_check']['show_reliance_require']))
                <li>Avvalimento necessario</li>
            @endif
            @if(!empty($filters['reliance_admit_check']['show_reliance_admit']))
                <li>Avvalimento permesso</li>
            @endif
            @if(!empty($filters['service_reference_require_check']['show_service_reference_require']))
                <li>Referenze servizi necessarie</li>
            @endif
            @if(!empty($filters['bank_reference_require_check']['show_bank_reference_require']))
                <li>Referenze bancarie necessarie</li>
            @endif
            @if(!empty($filters['pass_oe_require_check']['show_pass_oe_require']))
                <li>Previsto PASS OE</li>
            @endif
            @if(!empty($filters['deposit_require_check']['show_deposit_require']))
                <li>Cauzione provvisoria richiesta</li>
            @endif
            @if(!empty($filters['authority_tax_require_check']['show_authority_tax_require']))
                <li>Contributoautorità previsto</li>
            @endif
            @if(!empty($filters['project_require_check']['show_project_require']))
                <li>Realizzazione progetto</li>
            @endif
        </ul>
    @endif
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Gestione</th>
                <th>Condizioni</th>
                <th>Scadenza</th>
                <th>Sopralluogo</th>
                <th>Aperta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tenders as $tender)
                <tr class="tender-first-row">
                    <td colspan="6">
                        <strong>Gara:</strong> {{ Str::limit($tender->bidding->description, 150, '...') ?: 'N/D' }}
                    </td>
                </tr>
                <tr class="tender-second-row">
                    <td>{{ $tender->client?->name ?? 'N/D' }}</td>
                    <td>{{ $tender->manage_current . " - " . $tender->manage_offer }}</td>
                    <td>{{ $tender->conditions ? Str::limit($tender->conditions, 50, '...') : 'N/D' }}</td>
                    <td>{{ $tender->bidding->deadline_date ? \Carbon\Carbon::parse($tender->bidding->deadline_date)->format('d/m/Y') : 'N/D' }}</td>
                    <td>{{ $tender->bidding->inspection_deadline_date ? \Carbon\Carbon::parse($tender->bidding->inspection_deadline_date)->format('d/m/Y') : 'N/D' }}</td>
                    <td>{{ $tender->open_procedure_check ? 'Sì' : 'No' }}</td>
                </tr>
                <tr class="tender-third-row">
                    <td colspan="6">
                        <strong>Servizi:</strong> {{ $tender->bidding->serviceTypes->pluck('name')->join(' - ') ?: 'N/D' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
