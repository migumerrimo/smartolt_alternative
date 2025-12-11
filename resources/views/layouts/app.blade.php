<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INTERSANPABLO</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 250px;
            --content-padding: 2rem;
        }

        body {
            font-size: .9rem;
        }

        .sidebar {
            height: 100vh;
            background-color: #00C853;
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            overflow-y: auto;
            padding: 1rem 0.5rem;
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link {
            color: #fff;
            padding: .75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .sidebar .nav-link.active, 
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,.2);
            font-weight: bold;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 0.5rem;
            gap: 0.5rem;
            flex-direction: column;
            margin-bottom: 1.5rem;
        }

        .logo-container img {
            height: 80px;
            width: auto;
            object-fit: contain;
        }

        .logo-text {
            font-weight: bold;
            color: #fff;
            font-size: 0.85rem;
            line-height: 1.3;
            text-align: center;
        }

        /* Main content - dinámico */
        main {
            margin-left: var(--sidebar-width);
            padding: var(--content-padding);
            min-height: 100vh;
            transition: margin-left 0.3s ease, padding 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 0;
                --content-padding: 1rem;
            }

            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            main {
                margin-left: 0;
            }
        }

        /* Alertas */
        .alert {
            margin-bottom: 1.5rem;
        }

        /* Mejora visual */
        .nav-link i {
            margin-right: 0.5rem;
        }
    </style>

    @yield('styles')
</head>
<body>
<div class="container-fluid">
    <div class="row g-0">
        <!-- Sidebar -->
        <nav class="sidebar d-none d-md-block" id="sidebar">
            <div class="logo-container">
                <img src="{{ asset('images/intersanpablo-logo.png') }}" alt="INTERSANPABLO" />
                <div class="logo-text">INTERSANPABLO</div>
            </div>

            <ul class="nav flex-column">
                <li><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-house-fill"></i> Dashboard</a></li>
                <li><a class="nav-link {{ request()->is('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><i class="bi bi-people-fill"></i> Clientes</a></li>
                <li><a class="nav-link {{ request()->is('olts*') ? 'active' : '' }}" href="{{ route('olts.index') }}"><i class="bi bi-hdd-network-fill"></i> OLTs</a></li>
                <li><a class="nav-link {{ request()->is('onus*') ? 'active' : '' }}" href="{{ route('onus.index') }}"><i class="bi bi-router-fill"></i> ONUs</a></li>
                <li><a class="nav-link {{ request()->is('vlans*') ? 'active' : '' }}" href="{{ route('vlans.index') }}"><i class="bi bi-diagram-3-fill"></i> VLANs</a></li>
                <li><a class="nav-link {{ request()->is('alarms*') ? 'active' : '' }}" href="{{ route('alarms.index') }}"><i class="bi bi-exclamation-triangle-fill"></i> Alertas</a></li>
                <li><a class="nav-link {{ request()->is('dba-profiles*') ? 'active' : '' }}" href="{{ route('dba-profiles.index') }}"><i class="bi bi-list-columns-reverse"></i> Perfiles DBA</a></li>
                <li><a class="nav-link {{ request()->is('change-history*') ? 'active' : '' }}" href="{{ route('change-history.index') }}"><i class="bi bi-clock-history"></i> Historial</a></li>
                <li><a class="nav-link {{ request()->is('service-profiles*') ? 'active' : '' }}" href="{{ route('service-profiles.index') }}"><i class="bi bi-box-fill"></i> Perfiles</a></li>
            </ul>

            <hr class="bg-white mt-3">

            <ul class="navbar-nav">
                <li class="nav-item dropdown w-100">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        👤 {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="dropdown-item-text small"><strong>Rol:</strong> {{ Auth::user()->role }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    🚪 Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Toggle sidebar en móvil (opcional)
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');
    }
</script>

@yield('scripts')
</body>
</html>