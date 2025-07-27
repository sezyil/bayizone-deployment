<!-- Item Table -->



{{-- tailwind responsive table --}}
<div class="overflow-x-auto">
    <div class="-mx-4 mt-8 flow-root sm:mx-0">
        <table class="min-w-full ">
            <colgroup>
                <col class="w-1/4">
                <col class="w-1/12">
                <col class="w-1/12">
                @if (!$international)
                    <col class="w-1/12">
                @endif
                <col class="w-1/12">
                <col class="w-1/12">
                <col class="w-1/12">
                <col class="w-1/12">
            </colgroup>
            <thead class="border-b border-gray-300 text-gray-900">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                        {{ __('proforma.product') }}
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ __('proforma.quantity') }}
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ __('proforma.unit_price') }}
                    </th>
                    @if (!$international)
                        <th scope="col"
                            class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                            {{ __('proforma.tax') }}
                        </th>
                    @endif
                    {{-- discount --}}
                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ __('proforma.discount') }}
                    </th>

                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ __('proforma.volume') }}
                    </th>
                    {{-- package --}}
                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ __('proforma.package') }}
                    </th>

                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ __('proforma.total') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr class="border-b border-gray-200">
                        <td class="px-1 py-1 text-right text-sm text-gray-500 sm:table-cell">
                            <div class="flex items items-center">
                                <div class="flex-shrink-0 w-12 h-12">
                                    <img class="w-12 h-12 rounded" src="{{ asset($item->product_image_url) }}"
                                        alt="">
                                </div>
                                <div class="ml-4 flex flex-col items-start">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $item->product?->description->name ?? $item->product_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $item->product_description }}
                                    </div>
                                    {{-- note --}}
                                    @if ($item->note)
                                        <div class="flex flex-wrap mt-1 gap-1" role="note">
                                            <div
                                                class="px-2 py-1 mr-2 text-xs
                                                text-start text-gray-500 bg-gray-200 rounded">
                                                {{ __('proforma.product_note') }}:
                                                {{ $item->note }}
                                            </div>
                                        </div>
                                    @endif
                                    {{-- variants --}}
                                    @if ($item->variants->count() > 0)
                                        <div class="flex flex-wrap mt-1 gap-0.5  pb-0.5 flex-col" role="variant">
                                            @foreach ($item->variants as $variant)
                                                @php
                                                    $_variant = $variant->transformData(true, true);
                                                @endphp
                                                <div class="px-2 text-xs text-start ">
                                                    -{{ $_variant['variant']['name'] }}:
                                                    @if ($variant->type == App\Enums\VariantTypesEnum::COLOR->value)
                                                        {{ $_variant['variant_value']['name'] }}
                                                    @elseif ($variant->type == App\Enums\VariantTypesEnum::DIMENSION->value)
                                                        @if ($_variant['variant_value']['value']['length'] > 0)
                                                            @lang('proforma.length'):
                                                            {{ $_variant['variant_value']['value']['length'] }} cm,
                                                        @endif
                                                        @if ($_variant['variant_value']['value']['width'] > 0)
                                                            @lang('proforma.width'):
                                                            {{ $_variant['variant_value']['value']['width'] }} cm,
                                                        @endif
                                                        @if ($_variant['variant_value']['value']['height'] > 0)
                                                            @lang('proforma.height'):
                                                            {{ $_variant['variant_value']['value']['height'] }} cm
                                                        @endif
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- variants --}}

                            </div>
                        </td>
                        <td class="px-1 py-1 text-right text-sm text-gray-500 sm:table-cell">
                            <div class="flex flex-col items-end">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $item->quantity }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    ({{ $item->product?->unit?->description?->name ?? $item->product_unit }})
                                </div>
                            </div>
                        </td>
                        <td class="px-1 py-1 text-right text-sm text-gray-500 sm:table-cell">
                            {{ $currencyFormat($item->unit_price) }}
                        </td>
                        @if (!$international)
                            <td class="px-1 py-1 text-right text-sm text-gray-500 sm:table-cell">
                                {{ $item->tax_rate }}%
                            </td>
                        @endif
                        <td class="px-1 py-1 text-right text-sm text-gray-500 sm:table-cell">
                            <div class="flex flex-col items-end">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $currencyFormat($item->unit_discount) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    ({{ $item->unit_discount_rate }}%)
                                </div>
                            </div>
                        </td>

                        <td class="px-1 py-1 text-right text-sm text-gray-500 sm:table-cell">
                            {{-- unit_volume --}}
                            {{ $item->unit_volume }} m³
                        </td>
                        {{-- package --}}
                        <td class="px-1 py-1 text-right text-sm text-gray-500 sm:table-cell">
                            {{ $item->unit_package }}
                        </td>

                        <td class="px-1 py-1 text-right text-sm text-gray-500 sm:table-cell">
                            {{ $currencyFormat($item->grand_total) }}
                        </td>
                    </tr>
                @endforeach

            </tbody>
            <tfoot>
                <tr class="border-b border-gray-200">
                    {{-- totals --}}
                    <th class="px-1 py-1 text-left text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ __('proforma.total') }}
                    </th>
                    <th class="px-1 py-1 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ $calculateQuantity() }}
                    </th>
                    <th class="px-1 py-1 text-right text-sm font-semibold text-gray-900 sm:table-cell">

                    </th>
                    @if (!$international)
                        <th class="px-1 py-1 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        </th>
                    @endif
                    <th class="px-1 py-1 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ $currencyFormat($total_discount) }}
                    </th>
                    <th class="px-1 py-1 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ $calculateVolume() }} m³
                    </th>
                    <th class="px-1 py-1 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ $calculatePackage() }}
                    </th>
                    <th class="px-1 py-1 text-right text-sm font-semibold text-gray-900 sm:table-cell">
                        {{ $currencyFormat($grand_total) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
