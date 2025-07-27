<table>
    {{-- sipariş notu --}}
    <tbody>
        <tr>
            <td rowspan="2" style="vertical-align: middle;text-align:left;">
                @lang('proforma.order_note')
            </td>
            <td colspan="3" rowspan="2"
                style="width: 100px;word-wrap: break-word;vertical-align: middle;text-align:left;">
                @if ($data->note)
                    {{ $data->note }}
                @else
                    ---
                @endif
            </td>
        </tr>
    </tbody>
</table>

<table>
    <tbody>
        <tr>
            <td style="vertical-align: middle;text-align:left;">
                @lang('proforma.component.general.delivery_type')
            </td>
            <td colspan="3" style="width: 100px;word-wrap: break-word;vertical-align: middle;text-align:left;">
                @if ($data->delivery_type)
                    {{ $data->delivery_type }}
                @else
                    ---
                @endif
            </td>
        </tr>
        {{-- ödeme şekli --}}
        <tr>
            <td style="vertical-align: middle;text-align:left;">
                @lang('proforma.component.general.payment_type')
            </td>
            <td colspan="3" style="width: 100px;word-wrap: break-word;vertical-align: middle;text-align:left;">
                @if ($data->payment_type)
                    {{ $data->payment_type }}
                @else
                    ---
                @endif
            </td>
        </tr>
        {{-- incoterms --}}
        {{-- <tr>
            <td style="vertical-align: middle;text-align:left;">
                Incoterms
            </td>
            <td colspan="3" style="width: 100px;word-wrap: break-word;vertical-align: middle;text-align:left;">
                @if ($data->incoterms)
                    {{ $data->incoterms }}
                @else
                    ---
                @endif
            </td>
        </tr> --}}
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="4" style="background-color: #f0f0f0;border: 1px solid #000;text-align:left;">
                <h4>{{ __('proforma.invoice_title') }}</h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="background-color: #f0f0f0;border: 1px solid #000;text-align:left;">
                {{ __('proforma.invoice_address') }}: {{ $data->billing_address }}
            </td>
        </tr>
    </tbody>
</table>
