<div class="sidebar sidebar-light sidebar-main sidebar-expand-lg">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-section sidebar-user my-1">
            <div class="sidebar-section-body">
                <div class="media">
                    <a href="#" class="mr-3">
                        <img src="/assets/manager/images/placeholders/placeholder.jpg" class="rounded-circle"
                            alt="">
                    </a>

                    <div class="media-body">
                        <div class="font-weight-semibold">{{ Auth::user()->fullname }}</div>
                        <div class="font-size-sm line-height-sm opacity-50">
                            Admin
                        </div>
                    </div>

                    <div class="ml-3 align-self-center">
                        <button type="button"
                            class="btn btn-outline-light text-body border-transparent btn-icon rounded-pill btn-sm sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
                            <i class="icon-transmission"></i>
                        </button>

                        <button type="button"
                            class="btn btn-outline-light text-body border-transparent btn-icon rounded-pill btn-sm sidebar-mobile-main-toggle d-lg-none">
                            <i class="icon-cross2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /user menu -->


        <!-- Main navigation -->
        <div class="sidebar-section">
            <ul class="nav nav-sidebar" data-nav-type="accordion">
                {{-- müşteriler --}}
                <li class="nav-item">
                    <a href="{{ route('admin.customer.index') }}" class="nav-link">
                        <i class="icon-users"></i>
                        <span>Müşteriler</span>
                    </a>
                </li>

                {{-- siparişler --}}
                <li class="nav-item">
                    <a href="{{ route('admin.order.index') }}" class="nav-link">
                        <i class="icon-cart2"></i>
                        <span>Siparişler</span>
                    </a>
                </li>

                {{-- kuponlar --}}
                <li class="nav-item">
                    <a href="{{ route('admin.coupon.index') }}" class="nav-link">
                        <i class="icon-price-tag"></i>
                        <span>Kuponlar</span>
                    </a>
                </li>

                <!-- /page kits -->

            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            /* find current page url */
            var url = window.location;
            // for sidebar menu entirely but not cover treeview
            $('ul.nav-sidebar a').filter(function() {
                return this.href == url;
            }).addClass('active');
        });
    </script>
@endpush
