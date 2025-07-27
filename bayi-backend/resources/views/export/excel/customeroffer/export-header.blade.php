<table>
    <tbody>
        <tr>
            <td style="text-align:left;">
                @lang('proforma.offer_no')
            </td>
            <td style="text-align: left;">
                {{ $data->offer_no }}
            </td>
            <td rowspan="6" colspan="3" style="text-align: center;vertical-align: middle;">
                <p>
                    <strong>{{ $data->customer->firm_name }}</strong>
                </p>
                <p>
                    <strong>{{ $data->customer->address }}</strong>
                </p>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;">
                @lang('proforma.offer_date')
            </td>
            <td style="text-align: left;">
                {{ $data->offer_date }}
            </td>
        </tr>
        <tr>
            <td style="text-align:left;">
                @lang('proforma.offer_due_date')
            </td>
            <td style="text-align: left;">
                {{ $data->offer_due_date }}
            </td>
        </tr>
        <tr>
            <td style="text-align:left;">
                @lang('proforma.component.customer.name')
            </td>
            <td style="text-align: left;">
                {{ $data->company_customer->company_name }}
            </td>
        </tr>
        <tr>
            <td style="text-align:left;">
                @lang('proforma.component.customer.address')
            </td>
            <td style="text-align: left;">
                {{ $data->company_customer->address }}
            </td>
        </tr>
        {{-- currency --}}
        <tr>
            <td style="text-align:left;">
                @lang('proforma.currency')
            </td>
            <td style="text-align: left;">
                {{ $data->getCurrency->title }} ({{ $data->getCurrency->sign }})
            </td>
        </tr>
    </tbody>
</table>
