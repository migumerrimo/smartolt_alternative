<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de OLTs</title>
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
        .status-maintenance {
            color: #f39c12;
            font-weight: bold;
        }
        .onu-stats {
            font-size: 11px;
            color: #7f8c8d;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de OLTs del Sistema</h1>
        <div class="subtitle">
            Generado el: {{ $date }} | Sistema FreeOLT
        </div>
    </div>

    <!-- Resumen Estadístico -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-number">{{ $totalOlts }}</div>
                <div class="summary-label">Total OLTs</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $activeOlts }}</div>
                <div class="summary-label">OLTs Activas</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $totalOnus }}</div>
                <div class="summary-label">Total ONUs</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $onlineOnus }}</div>
                <div class="summary-label">ONUs Online</div>
            </div>
        </div>
    </div>

    <!-- Tabla de OLTs -->
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Modelo</th>
                <th>IP Management</th>
                <th>Estado</th>
                <th>ONUs</th>
                <th>Ubicación</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($olts as $olt)
            <tr>
                <td><strong>#{{ $olt->id }}</strong></td>
                <td>{{ $olt->name }}</td>
                <td>{{ $olt->model }}</td>
                <td>{{ $olt->management_ip }}</td>
                <td>
                    @if($olt->status === 'active')
                        <span class="status-active">● ACTIVA</span>
                    @elseif($olt->status === 'inactive')
                        <span class="status-inactive">● INACTIVA</span>
                    @else
                        <span class="status-maintenance">● MANTENIMIENTO</span>
                    @endif
                </td>
                <td>
                    <div class="onu-stats">
                        <strong class="text-success">{{ $olt->online_onus_count ?? 0 }}</strong> online / 
                        <strong>{{ $olt->total_onus_count ?? 0 }}</strong> total
                    </div>
                    @php
                        $total = $olt->total_onus_count ?? 1;
                        $online = $olt->online_onus_count ?? 0;
                        $percentage = $total > 0 ? ($online / $total) * 100 : 0;
                    @endphp
                    <small>{{ number_format($percentage, 1) }}% conectadas</small>
                </td>
                <td>{{ $olt->location ?? 'N/A' }}</td>
                <td>{{ $olt->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Información Adicional -->
    <div class="footer">
        <p>
            Reporte generado automáticamente por el Sistema FreeOLT | 
            Página <span class="page-number"></span>
        </p>
        <p>
            Este documento contiene información confidencial de la infraestructura de red.
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