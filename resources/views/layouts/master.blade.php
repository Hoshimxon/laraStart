
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Larastart</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->

    <link rel="stylesheet" href="{{mix('css/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper" id="app">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- SEARCH FORM -->
        <form class="form-inline ml-3">
            <div class="input-group input-group-sm">
                <input v-model="search" @keyup.enter.prevent="searchIt" class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button @click.prevent="searchIt" class="btn btn-navbar">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="#" class="brand-link">
            <img src="./img/AdminLTELogo.png" alt="Admin Logo" class="brand-image img-circle elevation-3"
                 style="opacity: .8">
            <span class="brand-text font-weight-light">Admin panel</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    @if(\Illuminate\Support\Facades\Auth::user()->photo)
                    <img src="{{asset('img/profile/'.\Illuminate\Support\Facades\Auth::user()->photo)}}" class="img-circle elevation-2" alt="User Image">
                    @else
                    <img src="./img/avatar.png" class="img-circle elevation-2" alt="User Image">
                    @endif
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{\Illuminate\Support\Facades\Auth::user()->name}}</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <router-link to="/dashboard" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt blue"></i>
                            <p>
                                Dashboard
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item has-treeview menu-open">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-info green"></i>
                            <p>
                                Site information
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <router-link to="/menus" class="nav-link">
                                    <i class="fas fa-bars nav-icon"></i>
                                    <p>Menu</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/slides" class="nav-link">
                                    <i class="fas fa-sliders-h nav-icon"></i>
                                    <p>Slides</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/advantages" class="nav-link">
                                    <i class="fas fa-star nav-icon"></i>
                                    <p>Advantages</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/how-to" class="nav-link">
                                    <i class="fas fa-question nav-icon"></i>
                                    <p>How to</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/news" class="nav-link">
                                    <i class="fas fa-newspaper nav-icon"></i>
                                    <p>News</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/settings" class="nav-link">
                                    <i class="fas fa-cogs nav-icon"></i>
                                    <p>Settings</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/feedback" class="nav-link">
                                    <i class="fas fa-mail-bulk nav-icon"></i>
                                    <p>Feedback</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>
                    @can('isAdmin')
                    <li class="nav-item has-treeview menu-open">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog green"></i>
                            <p>
                                Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <router-link to="/users" class="nav-link">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p>Users</p>
                                </router-link>
                            </li>

                            <li class="nav-item">
                                <router-link to="/developer" class="nav-link">
                                    <i class="fas fa-cogs"></i>
                                    <p>Developer</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <router-link to="/profile" class="nav-link">
                            <i class="nav-icon fas fa-user-alt orange"></i>
                            <p>
                               Profile
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            <i class="nav-icon fas fa-power-off red"></i>
                            <p>{{ __('Logout') }}</p>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <router-view></router-view>
                <vue-progress-bar></vue-progress-bar>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

@auth
    <script>
        window.user = @json(auth()->user());
        window.translations = @json(config('translatable.locales'));
        window.language = @json(app()->getLocale());
    </script>
@endauth

<script src="{{mix('js/app.js')}}"></script>

</body>
</html>
