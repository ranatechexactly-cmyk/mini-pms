<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard - PMS')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: var(--secondary-color);
            min-height: 100vh;
            display: flex;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: #fff;
            min-height: 100vh;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            letter-spacing: 0.05rem;
            z-index: 1;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            margin: 0 1rem 1rem;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            color: #d1d3e2;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            border-left: 0.25rem solid transparent;
            transition: all 0.3s;
        }
        
        .nav-link i {
            font-size: 0.85rem;
            margin-right: 0.25rem;
            width: 20px;
            text-align: center;
        }
        
        .nav-link:hover, .nav-link.active {
            color: #b7b9cc;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--primary-color);
        }
        
        /* Main Content */
        #content-wrapper {
            width: 100%;
            overflow-x: hidden;
        }
        
        /* Top Navigation */
        .topbar {
            height: 4.375rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            background-color: #fff;
            padding: 0.5rem 1.5rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        /* Stats Cards */
        .stat-card {
            border-left: 0.25rem solid var(--primary-color);
        }
        
        .stat-card .card-body {
            padding: 1rem 1.25rem;
        }
        
        .stat-card .text-xs {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #b7b9cc;
            letter-spacing: 0.05rem;
        }
        
        .stat-card .h5 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #5a5c69;
        }
        
        .stat-card .icon {
            color: #dddfeb;
            font-size: 2rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            .sidebar.toggled {
                margin-left: 0;
            }
            
            #content-wrapper {
                min-width: 100%;
            }
        }
    </style>
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
            <!-- Add more navigation items here -->
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
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                            Settings
                        </a>
                        <div class="dropdown-divider"></div>
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
        document.getElementById('sidebarToggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sidebar').classList.toggle('toggled');
        });
        
        // Close any open menu accordions when window is resized below 768px
        window.addEventListener('resize', function() {
            if (window.innerWidth < 768) {
                document.getElementById('sidebar').classList.add('toggled');
            } else {
                document.getElementById('sidebar').classList.remove('toggled');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
