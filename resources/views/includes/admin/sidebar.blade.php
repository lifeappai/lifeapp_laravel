<style>
    .slide-menu .slide-item:before {
        content: "\f0a4";
        font-family: 'FontAwesome' !important;
    }
</style>
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="main-sidebar app-sidebar sidebar-scroll">
    <div class="main-sidebar-header">
        <a class="desktop-logo logo-light active" style="display:contents;" href="{{ route('home') }}"
            class="text-center mx-auto"><img style="height: 62px;width: 120px;"
                src="{{ asset('assets/img/brand/logo.png') }}" class="main-logo"></a>
        <a class="desktop-logo icon-logo active" href="{{ route('home') }}"><img
                src="{{ asset('assets/img/brand/logo.png') }}" class="logo-icon"></a>
        <a class="desktop-logo logo-dark active" href="{{ route('home') }}"><img
                src="{{ asset('assets/img/brand/logo.png') }}" class="main-logo dark-theme" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ route('home') }}"><img
                src="{{ asset('assets/img/brand/logo.png') }}" class="logo-icon dark-theme" alt="logo"></a>
    </div><!-- /logo -->
    <div class="main-sidebar-loggedin">
        <div class="app-sidebar__user">
            <div class="dropdown user-pro-body text-center">
                <div class="user-pic">
                    <img src="{{ asset('assets/img/faces/6.jpg') }}" alt="user-img"
                        class="rounded-circle mCS_img_loaded">
                </div>
                <div class="user-info">
                    <h6 class=" mb-0 text-dark">
                        @if (Auth::user())
                            {{ Auth::user()->name }}
                        @endif
                    </h6>
                    <span class="text-muted app-sidebar__user-name text-sm">Administrator</span>
                </div>
            </div>
        </div>
    </div><!-- /user -->
    <div class="main-sidebar-body">
        <ul class="side-menu ">
            <li class="slide">
                <a class="side-menu__item @if (Route::currentRouteName() == 'home') active @endif"
                    href="{{ route('home') }}"><i class="side-menu__icon fa fa-tachometer"></i><span
                        class="side-menu__label">Dashboard</span></a>
            </li>

            <li class="slide">
                <a class="side-menu__item @if (Route::currentRouteName() == 'admin.subjects.index') active @endif"
                    href="{{ route('admin.subjects.index') }}"><i class="side-menu__icon fa fa-bullseye"></i><span
                        class="side-menu__label">Resources</span></a>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide"><i class="side-menu__icon fa fa-bullseye"></i><span
                        class="side-menu__label">Users</span><i class="angle fa fa-arrow-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin.users.list') }}">User List</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin.coupon.redeems.list') }}">User
                            Redeem Coins</a></li>
                    <li><a class="slide-item" href="{{ route('admin.la.missions.submissions') }}">Mission
                            Submissions</a>
                    </li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide"><i class="side-menu__icon fa fa-bullseye"></i><span
                        class="side-menu__label">Settings</span><i class="angle fa fa-arrow-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin.coupons.index') }}">Coupons</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin.languages.index') }}">Languages</a></li>
                    <li><a class="slide-item" href="{{ route('admin.schools.index') }}">Schools</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin.sections.index') }}">Sections</a></li>
                    <li><a class="slide-item" href="{{ route('admin.boards.index') }}">Boards</a></li>
                    <li><a class="slide-item" href="{{ route('admin.grades.index') }}">Grades</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin.get.states') }}">States</a></li>
                    <li><a class="slide-item" href="{{ route('admin.game.enrollments.index') }}">Game Enrollments</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin.game.enrollment.requests.index') }}">Game Enrollment Requests</a>
                    </li>

                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide"><i class="side-menu__icon fa fa-bullseye"></i><span
                        class="side-menu__label">Users</span><i class="angle fa fa-arrow-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin.mentors.index') }}">Mentors</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin.la.sessions.index') }}">Mentors Sessions</a></li>
                    <li><a class="slide-item" href="{{ route('admin.teachers.index') }}">Teachers</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide"><i class="side-menu__icon fa fa-bullseye"></i><span
                        class="side-menu__label">Teacher Resources</span><i class="angle fa fa-arrow-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin.competencies.index') }}">Competencies</a></li>
                    <li><a class="slide-item" href="{{ route('admin.concept.cartoons.headers') }}">Concept
                            Cartoon Header</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin.concept.cartoons.index') }}">Concept
                            Cartoons</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin.assessments.index') }}">Assessments</a></li>
                    <li><a class="slide-item" href="{{ route('admin.work.sheets.index') }}">Work
                            Sheets</a></li>
                    <li><a class="slide-item" href="{{ route('admin.lession.plan.languages.index') }}">Lession Plan
                            Languages</a></li>
                    <li><a class="slide-item" href="{{ route('admin.lession.plans.index') }}">Lession Plans</a>
                    </li>
                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item @if (Route::currentRouteName() == 'admin.push.notification.campaigns.index') active @endif"
                    href="{{ route('admin.push.notification.campaigns.index') }}"><i
                        class="side-menu__icon fa fa-bullseye"></i><span class="side-menu__label">Push Notification
                        Campaign</span></a>
            </li>
            <li class="slide">
                <a class="side-menu__item @if (Route::currentRouteName() == 'admin.statistics.index') active @endif"
                    href="{{ route('admin.statistics.index') }}"><i class="side-menu__icon fa fa-bullseye"></i><span
                        class="side-menu__label">Statistics</span></a>
            </li>
            <li class="slide">
                <a class="side-menu__item @if (Route::currentRouteName() == 'admin.chhattisgarh.status') active @endif"
                    href="{{ route('admin.chhattisgarh.status') }}"><i class="side-menu__icon fa fa-bullseye"></i><span
                        class="side-menu__label">Chhattisgarh</span></a>
            </li>
            <li class="slide">
                <a class="side-menu__item @if (Route::currentRouteName() == 'admin.bar-graph') active @endif"
                    href="{{ route('admin.bar-graph') }}"><i class="side-menu__icon fa fa-bullseye"></i><span
                        class="side-menu__label">Bar Graph</span></a>
            </li>
        </ul>
    </div>
</aside>
<!-- /main-sidebar -->
