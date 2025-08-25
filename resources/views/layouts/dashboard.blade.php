<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - PMS')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
            <div class="sidebar-brand-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="sidebar-brand-text mx-3">PMS</div>
        </a>
        
        <hr class="sidebar-divider my-0">
        
        <!-- Nav Items -->
        <div class="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @can('viewProjects')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                    <i class="fas fa-fw fa-project-diagram"></i>
                    <span>Projects</span>
                </a>
            </li>
            @endcan

            @can('viewTasks')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                    <i class="fas fa-fw fa-tasks"></i>
                    <span>Tasks</span>
                </a>
            </li>
            @endcan

            <!-- Users and Reports navigation items removed -->
        </div>
    </div>
    
    <!-- Content Wrapper -->
    <div id="content-wrapper">
        <!-- Top Navigation -->
        <nav class="topbar navbar navbar-expand navbar-light bg-white mb-4">
            <!-- Sidebar Toggle Button -->
            <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggle">
                <i class="fa fa-bars"></i>
            </button>
            
            <!-- Topbar Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                        <i class="fas fa-user-circle fa-fw"></i>
                    </a>
                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <div class="container-fluid px-4">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">@yield('header', 'Dashboard')</h1>
                @hasSection('actions')
                    <div class="d-flex">
                        @yield('actions')
                    </div>
                @endif
            </div>
            
            <!-- Content Row -->
            @yield('content')
            
        </div>
        <!-- End of Main Content -->
        
        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; {{ config('app.name') }} {{ date('Y') }}</span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->
    </div>
    <!-- End of Content Wrapper -->
    
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- Bootstrap core JavaScript-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom scripts -->
    <script>
        // Toggle the side navigation
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const contentWrapper = document.getElementById('content-wrapper');
            
            // Initialize sidebar state based on screen size
            function initSidebar() {
                if (window.innerWidth < 768) {
                    sidebar.classList.add('toggled');
                } else {
                    sidebar.classList.remove('toggled');
                }
            }
            
            // Toggle sidebar on button click
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle('toggled');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 768 && !sidebar.contains(e.target) && e.target !== sidebarToggle) {
                    sidebar.classList.add('toggled');
                }
            });
            
            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    initSidebar();
                }, 250);
            });
            
            // Initialize on page load
            initSidebar();
        });
    </script>
    
    @stack('scripts')
</body>
</html>
