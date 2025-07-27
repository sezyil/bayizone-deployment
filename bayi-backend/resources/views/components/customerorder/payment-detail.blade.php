<div class="grid grid-cols-2 gap-6 mt-4">
    <div class="border border-gray-200 rounded-lg shadow-md p-6">
        <h4 class="text-lg font-semibold mb-4">{{ __('proforma.invoice_title') }}</h4>
        <ul class="list-disc pl-4">
            <li class="mb-2">{{ __('proforma.invoice_address') }}: <span class="text-gray-600">{{ $billing_address }}</span></li>
        </ul>
    </div>

    <div class="border border-gray-200 rounded-lg shadow-md p-6">
        <h4 class="text-lg font-semibold mb-4">{{ __('proforma.component.general.title') }}</h4>
        <ul class="list-disc pl-4">
            <li class="mb-2">{{ __('proforma.component.general.delivery_type') }}: <span class="text-gray-600">{{ $delivery_type ?? '---' }}</span></li>
            <li class="mb-2">{{ __('proforma.component.general.payment_type') }}: <span class="text-gray-600">{{ $payment_type ?? '---' }}</span></li>
            {{-- <li>Incoterms: <span class="text-gray-600">{{ $incoterms ?? '---' }}</span></li> --}}
        </ul>
    </div>
</div>
