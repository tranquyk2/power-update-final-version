<table>
    <tr>
        <td style="font-weight: bold; text-align: center; border: 1px solid #000000;">Xưởng</td>
        <td style="font-weight: bold; text-align: center; border: 1px solid #000000; width: 120px;">KW</td>
        <td style="font-weight: bold; text-align: center; border: 1px solid #000000; width: 130px;">Thời gian</td>
    </tr>

    @foreach($histories as $history)
        <tr>
            <td style="border: 1px solid #000000;">{{ \App\Enums\DepartmentEnum::getDepartment($history->slave_id) }}</td>
            <td style="border: 1px solid #000000; width: 120px;">{{ $history->kw }}</td>
            <td style="border: 1px solid #000000; width: 130px;">{{ $history->datetime?->format('H:i d/m/Y') }}</td>
        </tr>
    @endforeach
</table>
