<table>
    <thead>
        <tr>
            <th>Factory</th>
            <th>Line</th>
            <th>Model</th>
            <th>Barcode</th>
            <th>Status</th>
            <th>Device Name</th>
            <th>Input/Output</th>
            <th>Datetime</th>
            <th>Note</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($histories as $history)
            @php
                $code = preg_replace('/[\x00-\x1F\x7F]/u', '', $history['code']);
            @endphp
            @if (preg_match('/[^a-zA-Z0-9]/', $code) == 1)
                @continue
            @endif

            <tr>
                <td>{{ $history['factory']['name'] ?? '' }}</td>
                <td>{{ $history['line']['name'] ?? '' }}</td>
                <td>{{ $history['model']['model'] ?? '' }}</td>
                <td>{{ $code }}</td>
                <td>{{ $history['status_name'] ?? '' }}</td>
                <td>{{ $history['device_name'] ?? '' }}</td>
                <td>{{ $history['type'] ?? '' }}</td>
                <td>{{ $history['datetime'] ?? '' }}</td>
                <td>{{ $history['note'] ?? '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
