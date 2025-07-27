<div class="grid grid-cols-2 gap-6 mt-4">
    <div class="border border-gray-200 rounded-lg shadow-md p-6">
        <h4 class="text-lg font-semibold mb-4">{{ __('proforma.component.payment.title') }}</h4>
        <ul class="list-disc pl-4">
            <li class="mb-2">{{ __('proforma.component.payment.bank_name') }}: <span class="text-gray-600">{{ $bank_name }}</span></li>
            <li class="mb-2">{{ __('proforma.component.payment.branch') }}: <span class="text-gray-600">{{ $branch_name }}</span></li>
            <li class="mb-2">{{ __('proforma.component.payment.account_name') }}: <span class="text-gray-600">{{ $account_name }}</span></li>
            <li class="mb-2">{{ __('proforma.component.payment.account_number') }}: <span class="text-gray-600">{{ $account_number }}</span></li>
            <li class="mb-2">IBAN: <span class="text-gray-600">{{ $iban }}</span></li>
            <li>{{ __('proforma.component.payment.swift') }}: <span class="text-gray-600">{{ $swift_code ?? '---' }}</span></li>
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
