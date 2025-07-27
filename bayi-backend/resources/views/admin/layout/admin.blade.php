<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        @yield('title', 'Bayizone')
    </title>
    @vite('resources/js/app.js')
    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/manager/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/manager/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('assets/manager/js/main/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/manager/js/main/bootstrap.bundle.min.js') }}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script src="{{ asset('assets/manager/js/plugins/visualization/d3/d3.min.js') }}"></script>
    <script src="{{ asset('assets/manager/js/plugins/visualization/d3/d3_tooltip.js') }}"></script>
    <script src="{{ asset('assets/manager/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/manager/js/plugins/pickers/daterangepicker.js') }}"></script>

    <script src="{{ asset('assets/manager/js/app.js') }}"></script>
    <!-- /theme JS files -->
    @stack('styles')

</head>

<body>

    <!-- Main navbar -->
    @include('admin.layout.main-navbar')
    <!-- /main navbar -->


    <!-- Page content -->
    <div class="page-content">

        <!-- Main sidebar -->
        @include('admin.layout.sidebar')
        <!-- /main sidebar -->


        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Inner content -->
            <div class="content-inner">

                <!-- Page header -->
                <div class="page-header">
                    <div class="page-header-content header-elements-lg-inline">
                        <div class="page-title d-flex">
                            <h4><i class="icon-arrow-left52 mr-2" onclick="window.history.back();"
                                    style="cursor: pointer;"></i>
                                @yield('title', 'Bayizone')
                            </h4>
                            <a href="#" class="header-elements-toggle text-body d-lg-none"><i
                                    class="icon-more"></i></a>
                        </div>

                        <div class="header-elements d-none mb-3 mb-lg-0">
                            @yield('header-buttons')
                        </div>
                    </div>
                </div>
                <!-- /page header -->


                <!-- Content area -->
                <div class="content pt-0">

                    @yield('content')

                </div>
                <!-- /content area -->


                <!-- Footer -->
                @include('admin.layout.footer')
                <!-- /footer -->

            </div>
            <!-- /inner content -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->
    @stack('scripts')
</body>

</html>
