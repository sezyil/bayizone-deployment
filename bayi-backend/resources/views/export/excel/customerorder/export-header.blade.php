<table>
    <tbody>
        <tr>
            <td style="text-align:left;">
                @lang('proforma.order_no')
            </td>
            <td style="text-align: left;">
                {{ $data->order_no }}
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
                @lang('proforma.order_date')
            </td>
            <td style="text-align: left;">
                {{ $data->order_date }}
            </td>
        </tr>
        <tr>
            <td style="text-align:left;">
                @lang('proforma.component.customer.name')
            </td>
            <td style="text-align: left;">
                {{ $data->companyCustomer->company_name }}
            </td>
        </tr>
        <tr>
            <td style="text-align:left;">
                @lang('proforma.component.customer.address')
            </td>
            <td style="text-align: left;">
                {{ $data->companyCustomer->address }}
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
