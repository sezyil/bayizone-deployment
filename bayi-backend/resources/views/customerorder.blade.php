<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=, initial-scale=0">
    <title>{{ $data->order_no }} | {{ __('proforma.order') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    @vite('resources/css/tailwind.css')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Montserrat', sans-serif !important;
        }

        @page {
            size: A4;
            margin: 0;
            height: 210mm;
            width: 297mm;
        }

        @media print {

            #button-container,
            #approve-container {
                display: none;
            }

            .page {
                margin: 0;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }

        /* print break page */
        .page {
            page-break-after: always;
        }
    </style>
</head>

<body class="bg-gray-100">
    {{-- language selector --}}
    <div class="flex justify-center items-center mt-2" id="button-container">
        <div class="flex flex-row items-center gap-2">
            <x-customerorder.language-selector :type="$type" :id="$data->id" :userid="$user_id ?? null" />
            <button id="btn-print"
                class="text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2 text-center inline-flex items-center dark:bg-orange-600 dark:hover:bg-orange-700 dark:focus:ring-orange-800">{{ __('proforma.print') }}</button>
        </div>
    </div>

    <div class="max-w-5xl mx-auto p-6 bg-white rounded shadow-sm my-6 page" id="invoice">

        <div class="grid grid-cols-2 items-center">
            <div>
                <!--  Company logo  -->
                <img src="{{ url($logo) }}" alt="company-logo" class="h-[100px]" />
            </div>

            <x-customerorder.company-detail :data="$data->customer" />
        </div>


        <!-- Client info -->
        <div class="grid grid-cols-2 items-center mt-8">
            <x-customerorder.customer-detail :data="$data" />
            <x-customerorder.invoice-detail :data="$data" :orderno="$data->order_no" />
        </div>




        <x-customerorder.item-table :data="$data" />



        <x-customerorder.payment-detail :data="$data" />



        <div class="bg-white rounded-lg shadow-md p-6 mt-3">
            <h4 class="text-lg font-semibold mb-4">{{ __('proforma.order_note') }}</h4>
            <p class="border-t border-gray-300 pt-4">{{ $data->note ?? __('proforma.order_note_not_found') }}</p>
        </div>

    </div>
    @vite('resources/js/flowbite.js')

    <script>
        //print button
        $('#btn-print').on('click', function(e) {
            e.preventDefault();
            window.print();
        });

        //language selector
    </script>


</body>

</html>
