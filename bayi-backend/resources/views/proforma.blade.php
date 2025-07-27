<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=, initial-scale=0">
    <title>{{ $data->offer_no }} | Proforma Fatura</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    @vite('resources/css/tailwind.css')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <style>
        /* font */
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
    </style>
</head>

<body class="bg-gray-100">
    {{-- language selector --}}
    <div class="flex justify-center items-center mt-2" id="button-container">
        <div class="flex flex-row items-center gap-2">
            <x-proforma.language-selector :type="$type" :id="$data->id" :userid="$user_id ?? null" />
            <button id="btn-print"
                class="text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2 text-center inline-flex items-center dark:bg-orange-600 dark:hover:bg-orange-700 dark:focus:ring-orange-800">{{ __('proforma.print') }}</button>
        </div>
    </div>

    {{-- language selector tailwind css --}}

    <div class="max-w-5xl mx-auto p-6 bg-white rounded shadow-sm my-6 page" id="invoice">

        <div class="grid grid-cols-2 items-center">
            <div>
                <!--  Company logo  -->
                <img src="{{ url($logo) }}" alt="company-logo" class="h-[100px]" />
            </div>

            <x-proforma.company-detail :data="$data->customer" />
        </div>


        <!-- Client info -->
        <div class="grid grid-cols-2 items-center mt-8">
            <x-proforma.customer-detail :data="$data" />

            <div>
                <x-proforma.invoice-detail :data="$data" />
                @if ($type == 'approval' && $changeable == 'true')
                    <div class="flex justify-end my-2" id="approve-container">
                        <a href="#" id="btn-approve"
                            class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md mr-2">
                            <i class="fas fa-check mr-1"></i>
                            {{ __('proforma.approve') }}
                        </a>
                        <a href="#" id="btn-reject"
                            class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-md">
                            <i class="fas fa-times mr-1"></i>
                            {{ __('proforma.reject') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Invoice Items -->
        <x-proforma.item-table :data="$data" />

        <x-proforma.payment-detail :data="$data" />

        <div class="bg-white rounded-lg shadow-md p-6 mt-3">
            <h4 class="text-lg font-semibold mb-4">{{ __('proforma.order_note') }}</h4>
            <p class="border-t border-gray-300 pt-4">{{ $data->note ?? __('proforma.proforma_note_not_found') }}</p>
        </div>

    </div>
    @vite('resources/js/flowbite.js')
    <script>
        const proformaId = "{{ $data->id }}"
        //print button
        $('#btn-print').on('click', function(e) {
            e.preventDefault();
            window.print();
        });
    </script>

    @if ($type == 'approval' && $changeable == 'true')
        <script>
            const disableButtons = () => {
                //ui loading class
                $('#btn-approve').addClass('disabled');
                $('#btn-reject').addClass('disabled');
            }
            const enableButtons = () => {
                //ui loading class
                $('.ui.button').removeClass('loading');
                $('#btn-approve').removeClass('disabled');
                $('#btn-reject').removeClass('disabled');
            }

            const sendData = (type, mail) => {
                $.ajax({
                    type: "POST",
                    url: "{{ route('proforma-invoice.approval', $data->id) }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: proformaId,
                        mail: mail,
                        password: '{{ $password }}',
                        type: type
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status === true) {
                            //redirect to proforma list
                            window.location.replace(
                                "{{ route('proforma-invoice.approval', ['proformaId' => $data->id, 'pass' => $password]) }}"
                            );
                        }
                    },
                    error: function(e) {
                        let response = e.responseJSON;
                        alert(response.errors.join('\n'));
                        enableButtons();
                    }
                });
            }

            //approve and cancel buttons for approval page
            $('#btn-approve').on('click', function(e) {
                e.preventDefault();
                //ask mail
                let mail = prompt("Mail adresinizi giriniz", "");
                if (mail == null || mail == "") {
                    alert("Mail adresi boş bırakılamaz");
                    return false;
                }

                let node = $(this);
                //add loading class
                node.addClass('loading');
                disableButtons();
                sendData('approve', mail);
            });

            $('#btn-reject').on('click', function(e) {
                e.preventDefault();
                let node = $(this);
                let mail = prompt("Mail adresinizi giriniz", "");
                if (mail == null || mail == "") {
                    alert("Mail adresi boş bırakılamaz");
                    return false;
                }

                //add loading class
                node.addClass('loading');
                disableButtons();
                sendData('reject', mail);
            });
        </script>
    @endif
</body>

</html>
