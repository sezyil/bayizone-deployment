@php
    $uri = 'customer-order.invoice';
    $redirParams =  ['orderId' => $id, 'pass' => $pass ?? ''];
    $list = [
        [
            'route' => route($uri, $redirParams + ['lang' => 'en']),
            'text' => 'English',
            app()->getLocale() == 'en' ? 'class' : '' => 'active item',
        ],
        [
            'route' => route($uri, $redirParams + ['lang' => 'tr']),
            'text' => 'Türkçe',
            app()->getLocale() == 'tr' ? 'class' : '' => 'active item',
        ],
    ];
@endphp
<div id="languageSelector">

    <button id="languageButton" data-dropdown-toggle="language"
        class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
        type="button">{{ __('proforma.language') }}<svg class="w-2.5 h-2.5 ms-3" aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 4 4 4-4" />
        </svg>
    </button>

    <!-- Dropdown menu -->
    <div id="language" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-22 dark:bg-gray-700">
        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="languageButton">
            @foreach ($list as $item)
                <li>
                    <a href="{{ $item['route'] }}"
                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                        {{ $item['text'] }}
                    </a>
                </li>
            @endforeach

        </ul>
    </div>
</div>
