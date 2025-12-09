<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codename FreeOLT</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-size: .9rem;
        }
        .sidebar {
            height: 155vh;
            background-color: #13a513ff;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: .75rem 1rem;
        }
        .sidebar .nav-link.active, 
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,.2);
            font-weight: bold;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 1rem;
            font-size: .8rem;
            color: #fff;
        }
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 0.5rem;
            gap: 0.5rem;
            flex-direction: column;
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
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar p-1">
            <div class="position-relative h-110">
                <!-- Logo y nombre -->
                <div class="logo-container">
                    <img src="{{ asset('images/intersanpablo-logo.png') }}" alt="INTERSANPABLO" />
                    <div class="logo-text">
                    </div>
                </div>

                <ul class="nav flex-column">
                    <li><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-house-fill"></i> Dashboard</a></li>
                    <li><a class="nav-link {{ request()->is('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><i class="bi bi-people-fill"></i> Clientes</a></li>
                    <li><a class="nav-link {{ request()->is('olts*') ? 'active' : '' }}" href="{{ route('olts.index') }}"><i class="bi bi-hdd-network-fill"></i> OLTs</a></li>
                    <li><a class="nav-link {{ request()->is('onus*') ? 'active' : '' }}" href="{{ route('onus.index') }}"><i class="bi bi-router-fill"></i> ONUs</a></li>
                    <li><a class="nav-link {{ request()->is('vlans*') ? 'active' : '' }}" href="{{ route('vlans.index') }}"><i class="bi bi-diagram-3-fill"></i> VLANs</a></li>
                    <li><a class="nav-link {{ request()->is('telemetry*') ? 'active' : '' }}" href="{{ route('telemetry.index') }}"><i class="bi bi-graph-up-arrow"></i> Telemetría</a></li>
                    <li><a class="nav-link {{ request()->is('alarms*') ? 'active' : '' }}" href="{{ route('alarms.index') }}"><i class="bi bi-exclamation-triangle-fill"></i> Alertas</a></li>
                    <li><a class="nav-link {{ request()->is('dba-profiles*') ? 'active' : '' }}" href="{{ route('dba-profiles.index') }}"><i class="bi bi-list-columns-reverse"></i> DBA Profiles</a></li>
                    <li><a class="nav-link {{ request()->is('device-configs*') ? 'active' : '' }}" href="{{ route('device-configs.index') }}"><i class="bi bi-gear-fill"></i> Configuración</a></li>
                    <li><a class="nav-link {{ request()->is('change-history*') ? 'active' : '' }}" href="{{ route('change-history.index') }}"><i class="bi bi-clock-history"></i> Historial</a></li>
                    <li><a class="nav-link {{ request()->is('service-profiles*') ? 'active' : '' }}" href="{{ route('service-profiles.index') }}"><i class="bi bi-box-fill"></i> Service Profiles</a></li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            👤 {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text small">
                                    <strong>Rol:</strong> {{ Auth::user()->role }}
                                </span>
                            </li>
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
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-4 py-4">
            @yield('content')
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>