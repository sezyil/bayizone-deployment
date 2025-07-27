<table>
    <tbody>
        <tr>
            <td style="text-align:left;">
                Tarih
            </td>
            <td style="text-align: left;">
                {{-- his --}}
                {{ now()->isoFormat('LLL') }}
            </td>

        </tr>
        <tr>
            <td style="text-align:left;">
                Rapor Başlangıç Tarihi
            </td>
            <td style="text-align: left;">
                {{ $startDate }}
            </td>
        </tr>
        <tr>
            <td style="text-align:left;">
                Rapor Bitiş Tarihi
            </td>
            <td style="text-align: left;">
                {{ $endDate }}
            </td>
        </tr>
        <tr>
            <td style="text-align:left;">
                Bakiye
            </td>
            <td style="text-align: left;">
                {{ $summary['formatted_balance'] }}
            </td>
        </tr>
        <tr>
            <td style="text-align:left;">
                Firma
            </td>
            <td style="text-align: left;">
                {{ $data->first()->customer->firm_name }}
            </td>
        </tr>
        {{-- currency --}}
        <tr>
            <td style="text-align:left;">
                @lang('proforma.currency')
            </td>
            <td style="text-align: left;">
                {{ $data->first()->getCurrency->title }} ({{ $data->first()->getCurrency->sign }})
            </td>
        </tr>
    </tbody>
</table>
