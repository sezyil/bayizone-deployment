<div class="navbar navbar-expand-lg navbar-light navbar-static">
    <div class="d-flex flex-1 d-lg-none">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
            <i class="icon-paragraph-justify3"></i>
        </button>
        <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
            <i class="icon-transmission"></i>
        </button>
    </div>

    <div class="navbar-brand text-center text-lg-left">
        <a href="{{ route('admin.home') }}" class="d-inline-block">
            <img src="/assets/manager/images/logo_light.png" class="d-none d-sm-block" alt="">
            <img src="/assets/manager/images/logo_icon_light.png" class="d-sm-none" alt="">
        </a>
    </div>

    <div class="collapse navbar-collapse order-2 order-lg-1" id="navbar-mobile">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a href="#" class="navbar-nav-link" data-toggle="dropdown">
                    <i class="icon-git-compare"></i>
                    <span class="d-lg-none ml-3">Git updates</span>
                    <span class="badge badge-warning badge-pill ml-auto ml-lg-0">9</span>
                </a>

                <div class="dropdown-menu dropdown-content wmin-lg-350">
                    <div class="dropdown-content-header">
                        <span class="font-weight-semibold">Git updates</span>
                        <a href="#" class="text-body"><i class="icon-sync"></i></a>
                    </div>

                    <div class="dropdown-content-body dropdown-scrollable">
                        <ul class="media-list">
                            <li class="media">
                                <div class="mr-3">
                                    <a href="#" class="btn btn-outline-primary rounded-pill border-2 btn-icon"><i
                                            class="icon-git-pull-request"></i></a>
                                </div>

                                <div class="media-body">
                                    Drop the IE <a href="#">specific hacks</a> for temporal inputs
                                    <div class="text-muted font-size-sm">4 minutes ago</div>
                                </div>
                            </li>

                            <li class="media">
                                <div class="mr-3">
                                    <a href="#" class="btn btn-outline-warning rounded-pill border-2 btn-icon"><i
                                            class="icon-git-commit"></i></a>
                                </div>

                                <div class="media-body">
                                    Add full font overrides for popovers and tooltips
                                    <div class="text-muted font-size-sm">36 minutes ago</div>
                                </div>
                            </li>

                            <li class="media">
                                <div class="mr-3">
                                    <a href="#" class="btn btn-outline-info rounded-pill border-2 btn-icon"><i
                                            class="icon-git-branch"></i></a>
                                </div>

                                <div class="media-body">
                                    <a href="#">Chris Arney</a> created a new <span
                                        class="font-weight-semibold">Design</span> branch
                                    <div class="text-muted font-size-sm">2 hours ago</div>
                                </div>
                            </li>

                            <li class="media">
                                <div class="mr-3">
                                    <a href="#" class="btn btn-outline-success rounded-pill border-2 btn-icon"><i
                                            class="icon-git-merge"></i></a>
                                </div>

                                <div class="media-body">
                                    <a href="#">Eugene Kopyov</a> merged <span
                                        class="font-weight-semibold">Master</span> and <span
                                        class="font-weight-semibold">Dev</span> branches
                                    <div class="text-muted font-size-sm">Dec 18, 18:36</div>
                                </div>
                            </li>

                            <li class="media">
                                <div class="mr-3">
                                    <a href="#" class="btn btn-outline-primary rounded-pill border-2 btn-icon"><i
                                            class="icon-git-pull-request"></i></a>
                                </div>

                                <div class="media-body">
                                    Have Carousel ignore keyboard events
                                    <div class="text-muted font-size-sm">Dec 12, 05:46</div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="dropdown-content-footer">
                        <a href="#" class="text-body mr-auto">All updates</a>
                        <div>
                            <a href="#" class="text-body" data-popup="tooltip" title="Mark all as read"><i
                                    class="icon-radio-unchecked"></i></a>
                            <a href="#" class="text-body ml-2" data-popup="tooltip" title="Bug tracker"><i
                                    class="icon-bug2"></i></a>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        <span class="badge badge-success my-3 my-lg-0 ml-lg-3">Online</span>


    </div>

    <ul class="navbar-nav flex-row order-1 order-lg-2 flex-1 flex-lg-0 justify-content-end align-items-center">
        <li class="nav-item nav-item-dropdown-lg dropdown dropdown-user h-100">
            <a href="#"
                class="navbar-nav-link navbar-nav-link-toggler dropdown-toggle d-inline-flex align-items-center h-100"
                data-toggle="dropdown">
                <img src="/assets/manager/images/placeholders/placeholder.jpg" class="rounded-pill" height="34"
                    alt="">
                <span class="d-none d-lg-inline-block ml-2">{{ Auth::user()->fullname }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('admin.auth.logout') }}" class="dropdown-item"><i class="icon-switch2"></i>
                    Çıkış Yap</a>
            </div>
        </li>
    </ul>
</div>
