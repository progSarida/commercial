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
    <h2 style="text-align: center"><u>Elenco Clienti</u></h2>
    @if(!empty($filters) || $search)
        <p><strong>Filtri applicati:</strong></p>
        <ul>
            @if($search)
                <li>Ricerca: {{ $search }}</li>
            @endif
            @if(!empty($filters['client_type']['values']))
                <li>
                    Tipo cliente:
                    @php
                        $clientTypeValues = array_map(function ($value) {
                            return \App\Enums\ClientType::tryFrom($value)?->getLabel() ?? $value;
                        }, $filters['client_type']['values']);
                    @endphp
                    {{ implode(', ', $clientTypeValues) }}
                </li>
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
        </ul>
    @endif
    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Codice univoco</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
                <tr>
                    <td>{{ $client->client_type->getLabel() }}</td>
                    <td>{{ trim($client->name) }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->ipa_code }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
