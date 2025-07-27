<table>
    <tbody>
        <tr>
            <td style="vertical-align: middle;text-align:left;">
                @lang('proforma.container_no')
            </td>
            <td colspan="3" style="width: 100px;word-wrap: break-word;vertical-align: middle;text-align:left;">
                {{ $data->container_no ?? '---' }}
            </td>
        </tr>
        {{-- ödeme şekli --}}
        <tr>
            <td style="vertical-align: middle;text-align:left;">
                @lang('proforma.carrier')
            </td>
            <td colspan="3" style="width: 100px;word-wrap: break-word;vertical-align: middle;text-align:left;">
                {{ $data->carrier ?? '---' }}
            </td>
        </tr>
        {{-- incoterms --}}
        <tr>
            <td style="vertical-align: middle;text-align:left;" rowspan="2">
                @lang('common.note')
            </td>
            <td colspan="3" style="width: 100px;word-wrap: break-word;vertical-align: middle;text-align:left;"
                rowspan="2">
                {{ $data->note ?? '---' }}
            </td>
        </tr>
    </tbody>
</table>
