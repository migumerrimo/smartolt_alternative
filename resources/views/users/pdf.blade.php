<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Usuarios</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            color: #7f8c8d;
            font-size: 14px;
        }
        .summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            text-align: center;
        }
        .summary-item {
            padding: 10px;
        }
        .summary-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th {
            background-color: #34495e;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        .table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status-active {
            color: #27ae60;
            font-weight: bold;
        }
        .status-inactive {
            color: #e74c3c;
            font-weight: bold;
        }
        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .role-admin {
            background: #e74c3c;
            color: white;
        }
        .role-technician {
            background: #3498db;
            color: white;
        }
        .role-user {
            background: #2ecc71;
            color: white;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Usuarios del Sistema</h1>
        <div class="subtitle">
            Generado el: {{ $date }} | Sistema FreeOLT
        </div>
    </div>

    <!-- Resumen Estadístico -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-number">{{ $totalUsers }}</div>
                <div class="summary-label">Total Usuarios</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $activeUsers }}</div>
                <div class="summary-label">Usuarios Activos</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $adminUsers }}</div>
                <div class="summary-label">Administradores</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $technicianUsers }}</div>
                <div class="summary-label">Técnicos</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo Electrónico</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td><strong>#{{ $user->id }}</strong></td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="role-badge role-{{ $user->role }}">
                        {{ strtoupper($user->role) }}
                    </span>
                </td>
                <td>
                    @if($user->active)
                        <span class="status-active">● ACTIVO</span>
                    @else
                        <span class="status-inactive">● INACTIVO</span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Información Adicional -->
    <div class="footer">
        <p>
            Reporte generado por FreeOLT | 
            Página <span class="page-number"></span>
        </p>
        <p>
            Este documento contiene información confidencial del sistema, su difusión puede constituir en un delito.
        </p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>