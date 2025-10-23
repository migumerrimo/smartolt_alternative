<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Diario - CODENAME FreeOLT ALPHA </title>
    <style>
        /* Estilos optimizados para PDF */
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        
        .header .subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .summary-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        
        .summary-card h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #6c757d;
        }
        
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        
        .summary-card.critical { border-left: 4px solid #dc3545; }
        .summary-card.warning { border-left: 4px solid #ffc107; }
        .summary-card.success { border-left: 4px solid #28a745; }
        .summary-card.info { border-left: 4px solid #17a2b8; }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section h2 {
            background: #2c3e50;
            color: white;
            padding: 8px 12px;
            margin: 0 0 15px 0;
            font-size: 16px;
            border-radius: 4px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table th {
            background: #34495e;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
        }
        
        table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        
        .badge.critical { background: #dc3545; }
        .badge.major { background: #fd7e14; }
        .badge.minor { background: #ffc107; color: #000; }
        .badge.success { background: #28a745; }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 10px;
        }
        
        .health-score {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
        }
        
        .text-success { color: #28a745; }
        .text-warning { color: #ffc107; }
        .text-danger { color: #dc3545; }
        .text-info { color: #17a2b8; }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>🌐 Reporte Diario de Red</h1>
        <div class="subtitle">
            FreeOLT - Sistema de Gestión de Red GPON
        </div>
        <div class="subtitle">
            Generado el: {{ $generatedAt }} | Período: {{ $reportDate }}
        </div>
    </div>
    
    <!-- Resumen Ejecutivo -->
    <div class="section">
        <h2>📊 Resumen Ejecutivo</h2>
        <div class="summary-grid">
            <div class="summary-card success">
                <h3>Salud del Sistema</h3>
                <div class="health-score">{{ $healthScore }}%</div>
            </div>
            <div class="summary-card info">
                <h3>OLTs Activas</h3>
                <p class="value">{{ $oltsActive }}/{{ $oltsTotal }}</p>
            </div>
            <div class="summary-card info">
                <h3>ONUs Online</h3>
                <p class="value">{{ $onusOnline }}/{{ $onusTotal }}</p>
            </div>
            <div class="summary-card warning">
                <h3>Uptime Sistema</h3>
                <p class="value">{{ $systemUptime }}</p>
            </div>
        </div>
    </div>
    
    <!-- Métricas de Rendimiento -->
    <div class="section">
        <h2>📈 Métricas de Rendimiento</h2>
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Ancho de Banda Promedio</h3>
                <p class="value">{{ $avgBandwidth }}</p>
            </div>
            <div class="summary-card">
                <h3>Latencia Promedio</h3>
                <p class="value">{{ $avgLatency }}</p>
            </div>
            <div class="summary-card critical">
                <h3>Alertas Críticas</h3>
                <p class="value">{{ $criticalAlarms }}</p>
            </div>
            <div class="summary-card warning">
                <h3>Alertas Mayores</h3>
                <p class="value">{{ $majorAlarms }}</p>
            </div>
        </div>
    </div>
    
    <!-- OLTs Principales -->
    <div class="section">
        <h2>🔌 OLTs Principales</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre OLT</th>
                    <th>Estado</th>
                    <th>IP Management</th>
                    <th>ONUs Online</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mainOlts as $olt)
                <tr>
                    <td>{{ $olt->name }}</td>
                    <td>
                        <span class="badge {{ $olt->status === 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($olt->status) }}
                        </span>
                    </td>
                    <td>{{ $olt->management_ip }}</td>
                    <td>{{ $olt->online_onus_count ?? 0 }}</td>
                    <td>{{ $olt->location ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Alertas Recientes -->
    @if($recentAlarms->count() > 0)
    <div class="section">
        <h2>🚨 Alertas Recientes</h2>
        <table>
            <thead>
                <tr>
                    <th>Severidad</th>
                    <th>Mensaje</th>
                    <th>OLT</th>
                    <th>ONU</th>
                    <th>Detectada</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentAlarms as $alarm)
                <tr>
                    <td>
                        <span class="badge {{ $alarm->severity }}">
                            {{ ucfirst($alarm->severity) }}
                        </span>
                    </td>
                    <td>{{ Str::limit($alarm->message, 50) }}</td>
                    <td>{{ $alarm->olt->name ?? 'N/A' }}</td>
                    <td>{{ $alarm->onu->serial_number ?? 'N/A' }}</td>
                    <td>{{ $alarm->detected_at->format('H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Pie de Página -->
    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema FreeOLT</p>
        <p>Próxima actualización: {{ now()->addDay()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>