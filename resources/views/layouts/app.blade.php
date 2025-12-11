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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
            overflow-x: hidden;
        }

        body {
            font-size: .9rem;
            display: flex;
            flex-direction: row;
        }

        /* SIDEBAR - FIJO EN DESKTOP */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #00C853;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1rem 0.5rem;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: #fff;
            padding: .75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            white-space: nowrap;
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

        /* MAIN CONTENT - FLEX GROW */
        main {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
            width: calc(100% - 250px);
            overflow-x: hidden;
            transition: all 0.3s ease;
        }

        /* ALERTAS */
        .alert {
            margin-bottom: 1.5rem;
        }

        .nav-link i {
            margin-right: 0.5rem;
        }

        /* ============================================ */
        /* RESPONSIVE - TABLET (768px) */
        /* ============================================ */
        @media (max-width: 992px) {
            .sidebar {
                width: 200px;
            }

            main {
                width: calc(100% - 200px);
                margin-left: 200px;
                padding: 1.5rem;
            }
        }

        /* ============================================ */
        /* RESPONSIVE - MOBILE (768px) */
        /* ============================================ */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                top: auto;
                padding: 0.5rem;
                border-top: 1px solid rgba(255,255,255,.1);
            }

            .sidebar .logo-container {
                display: none;
            }

            .sidebar .nav-flex-column {
                display: flex;
                flex-direction: row;
                gap: 0;
                overflow-x: auto;
            }

            .sidebar .nav-link {
                padding: 0.5rem;
                margin: 0;
                font-size: 0.75rem;
                flex: 1 0 auto;
                text-align: center;
            }

            main {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
                margin-bottom: 80px; /* Espacio para sidebar mobile */
                min-height: calc(100vh - 80px);
            }

            .sidebar hr {
                display: none;
            }

            .sidebar .navbar-nav {
                display: none;
            }
        }

        /* ============================================ */
        /* RESPONSIVE - TELÉFONO PEQUEÑO (576px) */
        /* ============================================ */
        @media (max-width: 576px) {
            main {
                padding: 0.75rem;
                margin-bottom: 80px;
            }

            .sidebar .nav-link {
                padding: 0.4rem 0.2rem;
                font-size: 0.65rem;
            }

            .sidebar .nav-link i {
                margin-right: 0.2rem;
            }

            .card {
                margin-bottom: 0.5rem;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="{{ asset('images/intersanpablo-logo.png') }}" alt="INTERSANPABLO" />
            <div class="logo-text">INTERSANPABLO</div>
        </div>

        <ul class="nav flex-column nav-flex-column">
            <li><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-house-fill"></i> <span class="d-none d-lg-inline">Dashboard</span></a></li>
            <li><a class="nav-link {{ request()->is('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><i class="bi bi-people-fill"></i> <span class="d-none d-lg-inline">Clientes</span></a></li>
            <li><a class="nav-link {{ request()->is('olts*') ? 'active' : '' }}" href="{{ route('olts.index') }}"><i class="bi bi-hdd-network-fill"></i> <span class="d-none d-lg-inline">OLTs</span></a></li>
            <li><a class="nav-link {{ request()->is('onus*') ? 'active' : '' }}" href="{{ route('onus.index') }}"><i class="bi bi-router-fill"></i> <span class="d-none d-lg-inline">ONUs</span></a></li>
            <li><a class="nav-link {{ request()->is('vlans*') ? 'active' : '' }}" href="{{ route('vlans.index') }}"><i class="bi bi-diagram-3-fill"></i> <span class="d-none d-lg-inline">VLANs</span></a></li>
            <li><a class="nav-link {{ request()->is('alarms*') ? 'active' : '' }}" href="{{ route('alarms.index') }}"><i class="bi bi-exclamation-triangle-fill"></i> <span class="d-none d-lg-inline">Alertas</span></a></li>
            <li><a class="nav-link {{ request()->is('dba-profiles*') ? 'active' : '' }}" href="{{ route('dba-profiles.index') }}"><i class="bi bi-list-columns-reverse"></i> <span class="d-none d-lg-inline">Perfiles DBA</span></a></li>
            <li><a class="nav-link {{ request()->is('change-history*') ? 'active' : '' }}" href="{{ route('change-history.index') }}"><i class="bi bi-clock-history"></i> <span class="d-none d-lg-inline">Historial</span></a></li>
            <li><a class="nav-link {{ request()->is('service-profiles*') ? 'active' : '' }}" href="{{ route('service-profiles.index') }}"><i class="bi bi-box-fill"></i> <span class="d-none d-lg-inline">Perfiles</span></a></li>
        </ul>

        <hr class="bg-white mt-3 d-none d-lg-block">

        <ul class="navbar-nav d-none d-lg-flex">
            <li class="nav-item dropdown w-100">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    👤 {{ Auth::user()->name ?? 'Usuario' }}
                </a>
                <ul class="dropdown-menu">
                    <li><span class="dropdown-item-text small"><strong>Rol:</strong> {{ Auth::user()->role ?? 'N/A' }}</span></li>
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

    <!-- Main Content -->
    <main>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @yield('scripts')
</body>
</html>