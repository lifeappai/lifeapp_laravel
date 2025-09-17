   <!-- main-header -->
   <div class="main-header  side-header">
       <div class="container-fluid">
           <div class="main-header-left ">
               <div class="app-sidebar__toggle mobile-toggle" data-toggle="sidebar">
                   <a class="open-toggle" href="#"><i class="header-icons" data-eva="menu-outline"></i></a>
                   <a class="close-toggle" href="#"><i class="header-icons" data-eva="close-outline"></i></a>
               </div>
               <div class="responsive-logo">
                   <a href="{{ url('/' . ($page = 'index')) }}"><img
                           src="{{ asset('assets/img/brand/logo.png') }}" class="logo-1"></a>
                   <a href="{{ url('/' . ($page = 'index')) }}"><img src="{{ asset('assets/img/brand/logo.png') }}"
                           class="logo-11"></a>
                   <a href="{{ url('/' . ($page = 'index')) }}"><img
                           src="{{ asset('assets/img/brand/logo.png') }}" class="logo-2"></a>
                   <a href="{{ url('/' . ($page = 'index')) }}"><img
                           src="{{ asset('assets/img/brand/logo.png') }}" class="logo-12"></a>
               </div>
           </div>
           <div class="main-header-right">
               <div class="nav nav-item  navbar-nav-right ml-auto">
                   <div class="dropdown main-profile-menu nav nav-item nav-link">

                       <a class="profile-user d-flex" href=""><img
                               src="{{ asset('assets/img/faces/6.jpg') }}" alt="user-img"
                               class="rounded-circle mCS_img_loaded"><span></span></a>

                       <div class="dropdown-menu">
                           <div class="main-header-profile header-img">
                               <div class="main-img-user"><img alt=""
                                       src="{{ asset('assets/img/faces/6.jpg') }}"></div>
                               <h6>
                                   @if (Auth::user())
                                       {{ Auth::user()->name }}
                                   @endif
                               </h6>
                           </div>
                           <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                               <i class="fa fa-sign-out"></i> {{ __('Logout') }}
                           </a>

                           <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                               @csrf
                           </form>
                       </div>
                   </div>

               </div>
           </div>
       </div>
   </div>
   <!-- /main-header -->
