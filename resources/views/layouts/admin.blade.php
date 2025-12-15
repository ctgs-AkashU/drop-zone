<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Sharda Stationary</title>

    <!-- Bootstrap 5.3 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 280px;
            --primary: #4361ee;
            --primary-dark: #3f37c9;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #0f172a 100%);
            color: #e2e8f0;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.2);
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link {
            color: #cbd5e1 !important;
            padding: 0.9rem 1.5rem;
            border-radius: 0.5rem;
            margin: 0.4rem 1rem;
            font-weight: 500;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: #fff !important;
            transform: translateX(8px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white !important;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.4);
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 24px;
        }

        .logout-btn {
            color: #fca5a5 !important;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: auto;
            padding-top: 1.5rem;
        }

        .logout-btn:hover {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #eecfcf !important;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar.show {
                transform: translateX(0);
            }
        }

        /* Mobile toggle button */
        .mobile-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--primary);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(67,97,238,0.4);
        }
    </style>
</head>
<body class="bg-light">

    <!-- Mobile Menu Toggle -->
    <button class="btn mobile-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
        Menu
    </button>

    <!-- Sidebar (Desktop + Offcanvas for Mobile) -->
    <div class="sidebar d-flex flex-column" id="sidebarOffcanvas">
        <!-- Brand -->
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                {{ env('APP_NAME', 'Sharda Stationery') }}
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-grow-1 px-3 py-4">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.uploads.index') }}"
               class="nav-link {{ request()->routeIs('admin.uploads.*') ? 'active' : '' }}">
                Upload Sessions
            </a>
            <a href="{{ route('admin.backgrounds.index') }}"
               class="nav-link {{ request()->routeIs('admin.backgrounds.*') ? 'active' : '' }}">
                Background Images
            </a>
        </nav>

        <!-- Logout -->
        <div class="px-3 pb-4">
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="nav-link logout-btn w-100 text-start">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid py-4 py-lg-5">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Optional: Auto-highlight active menu -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const current = location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === current || 
                    (current.includes('/uploads') && link.href.includes('/uploads')) ||
                    (current.includes('/backgrounds') && link.href.includes('/backgrounds'))) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>