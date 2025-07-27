<table>
    <thead>
        <tr>
            <th colspan="14" style="text-align: center;" bgcolor="#f0f0f0">
                @lang('proforma.products')
            </th>
        </tr>
        <tr>
            <th>
                @lang('proforma.order_no')
            </th>
            <th>
                @lang('proforma.image')
            </th>
            <th>
                @lang('proforma.product')
            </th>
            <th>
                @lang('proforma.color')
            </th>
            <th>
                @lang('proforma.dimension')
            </th>
            <th>
                @lang('proforma.quantity')
            </th>
            <th>
                @lang('proforma.unit_price')
            </th>
            <th style="text-color:red;">
                @lang('proforma.total_price')
            </th>
            {{-- Hacim --}}
            <th>
                @lang('proforma.volume')
            </th>
            <th>
                @lang('proforma.total_volume')
            </th>
            {{-- Paket --}}
            <th>
                @lang('proforma.package')
            </th>
            <th>
                @lang('proforma.total_package')
            </th>
            <th>
                @lang('proforma.unit_weight')
            </th>
            <th>
                @lang('proforma.total_weight')
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data->lines as $line)
            <tr>
                <td>

                    {{ $line->orderLine?->customer_order?->order_no ?? '---' }}
                </td>
                <td style="text-align: center;">
                    @if ($line->orderLine?->product?->image)
                        <a href="{{ url($line->orderLine?->product?->image) }}" target="_blank">
                            <img src="{{ public_path($line->orderLine?->product?->image) }}" height="50px">
                        </a>
                    @else
                        <img src="{{ public_path('images/no-image.png') }}" height="50px">
                    @endif
                </td>
                <td>
                    {{ $line->orderLine?->product?->name ?? ($line->orderLine?->product_name ?? '---') }}
                </td>
                <td>
                    @forelse ($line->orderLine?->variants()->colors()->get() as $colorItem)
                        <p>
                            @php
                                $_variant = $colorItem->transformData(true, true);
                            @endphp
                        <p class="">
                            -{{ $_variant['variant']['name'] }}:
                            @if ($colorItem->type == App\Enums\VariantTypesEnum::COLOR->value)
                                {{ $_variant['variant_value']['name'] }}
                            @endif
                        </p>
                    @empty
                        ---
                    @endforelse
                </td>
                <td>
                    @forelse ($line->orderLine?->variants()->dimensions()->get() as $dimensionItem)
                        <p>
                            @php
                                $_variant = $dimensionItem->transformData(true, true);
                            @endphp
                        <p class="">
                            -{{ $_variant['variant']['name'] }}:
                            <br />
                            @if ($dimensionItem->type == App\Enums\VariantTypesEnum::DIMENSION->value)
                                @lang('proforma.length'): {{ $_variant['variant_value']['value']['length'] }} cm x
                                @lang('proforma.width'): {{ $_variant['variant_value']['value']['width'] }} cm x
                                @lang('proforma.height'): {{ $_variant['variant_value']['value']['height'] }} cm
                            @endif
                        </p>
                    @empty
                        ---
                    @endforelse
                </td>
                <td>
                    {{ $line->quantity }}
                </td>
                <td style="text-align: right;">
                    {{ $data->getCurrency?->format($line->orderLine?->unit_price, true) }}
                </td>
                <td style="text-align: right;">
                    {{ $data->getCurrency?->format($line->orderLine?->grand_total, true) }}
                </td>
                {{-- Hacim --}}
                <td>
                    {{ $line->unit_volume }}
                </td>
                <td>
                    {{ $line->total_volume }}
                </td>
                {{-- Paket --}}
                <td>
                    {{ $line->unit_package }}
                </td>
                <td>
                    {{ $line->total_package }}
                </td>
                {{-- not --}}
                <td>
                    {{ $line->weight ?? '---' }}
                </td>
                <td>
                    {{ $line->total_weight ?? '---' }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="background-color: #f0f0f0;border: 1px solid #000;">
                @lang('proforma.total')
            </td>
            <td style="background-color: #f0f0f0;border: 1px solid #000;">
                {{ $data->lines->sum('quantity') }}
            </td>
            <td style="background-color: #f0f0f0;border: 1px solid #000;"></td>
            <td style="text-color:red;text-align: right;background-color: #f0f0f0;border: 1px solid #000;">
                {{ $data->getCurrency?->format($data->grand_total, true) }}
            </td>
            {{-- Hacim --}}
            <td style="background-color: #f0f0f0;border: 1px solid #000;"></td>
            <td style="text-color:red;background-color: #f0f0f0;border: 1px solid #000;">
                {{ $data->total_volume }}
            </td>
            {{-- Paket --}}
            <td style="background-color: #f0f0f0;border: 1px solid #000;"></td>
            <td style="text-color:red;background-color: #f0f0f0;border: 1px solid #000;">
                {{ $data->total_package }}
            </td>

            <td style="background-color: #f0f0f0;border: 1px solid #000;"></td>
            <td style="text-color:red;background-color: #f0f0f0;border: 1px solid #000;">
                {{ $data->total_weight }}
            </td>

        </tr>
    </tfoot>
</table>
