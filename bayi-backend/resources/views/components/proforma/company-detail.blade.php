{{-- <div class="ui card border-0">
    <div class="content">
        <div class="header">{{ __('proforma.component.company.title') }}</div>
    </div>
    <div class="content">
        <ul>
            <li> <strong> {{ __('proforma.component.company.name') }}: </strong> {{ $name }} </li>
            <li><strong> {{ __('proforma.component.company.address') }}: </strong> {{ $address }} </li>
            <li><strong> {{ __('proforma.component.company.phone') }}: </strong> {{ $phone }} </li>
            <li><strong> {{ __('proforma.component.company.email') }}: </strong> {{ $email }} </li>
            <li><strong> {{ __('proforma.component.company.contact') }}: </strong> {{ $contact }} </li>
        </ul>
    </div>
</div> --}}

<div class="text-right">
    <p>
        {{ $name }}
    </p>
    <p class="text-gray-500 text-sm">
        {{ $email }}
    </p>
    <p class="text-gray-500 text-sm mt-1">
        {{ $phone }}
    </p>
    <p class="text-gray-500 text-sm mt-1">
        {{ $contact }}
    </p>
    <p class="text-gray-500 text-sm mt-1">
        {{ $address }}
    </p>
</div>
