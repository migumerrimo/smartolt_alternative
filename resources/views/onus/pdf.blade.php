<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de ONUs</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 22px;
        }
        .header .subtitle {
            color: #7f8c8d;
            font-size: 12px;
        }
        .summary {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #3498db;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            text-align: center;
        }
        .summary-item {
            padding: 8px;
        }
        .summary-number {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-label {
            font-size: 9px;
            color: #7f8c8d;
            margin-top: 3px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9px;
        }
        .table th {
            background-color: #34495e;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
        }
        .table td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status-online {
            color: #27ae60;
            font-weight: bold;
        }
        .status-offline {
            color: #e74c3c;
            font-weight: bold;
        }
        .status-registered {
            color: #f39c12;
            font-weight: bold;
        }
        .serial-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 8px;
        }
        .customer-assigned {
            color: #27ae60;
            font-weight: bold;
        }
        .customer-not-assigned {
            color: #7f8c8d;
            font-style: italic;
        }
        .customer-multiple {
            background: #e8f5e8;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #7f8c8d;
            font-size: 8px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de ONUs del Sistema</h1>
        <div class="subtitle">
            Generado el: {{ $date }} | Sistema FreeOLT
        </div>
    </div>

    <!-- Resumen Estadístico -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-number">{{ $totalOnus }}</div>
                <div class="summary-label">Total ONUs</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $onlineOnus }}</div>
                <div class="summary-label">ONUs Online</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $offlineOnus }}</div>
                <div class="summary-label">ONUs Offline</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $registeredOnus }}</div>
                <div class="summary-label">ONUs Registradas</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $assignedOnus }}</div>
                <div class="summary-label">ONUs Asignadas</div>
            </div>
        </div>
    </div>

    <!-- Tabla de ONUs -->
    <table class="table">
        <thead>
            <tr>
                <th>Serial</th>
                <th>Modelo</th>
                <th>PON Port</th>
                <th>Estado</th>
                <th>OLT</th>
                <th>Señal RX</th>
                <th>Señal TX</th>
                <th>Cliente(s)</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($onus as $onu)
            <tr>
                <td class="serial-number">{{ $onu->serial_number }}</td>
                <td>{{ $onu->model }}</td>
                <td>{{ $onu->pon_port }}</td>
                <td>
                    @if($onu->status === 'online')
                        <span class="status-online">● ONLINE</span>
                    @elseif($onu->status === 'offline')
                        <span class="status-offline">● OFFLINE</span>
                    @elseif($onu->status === 'registered')
                        <span class="status-registered">● REGISTRADA</span>
                    @else
                        <span>{{ $onu->status }}</span>
                    @endif
                </td>
                <td>{{ $onu->olt->name ?? 'N/A' }}</td>
                <td>{{ $onu->rx_power ?? 'N/A' }} dBm</td>
                <td>{{ $onu->tx_power ?? 'N/A' }} dBm</td>
                <td>
                    {{-- USANDO LOS NUEVOS HELPERS --}}
                    @if($onu->has_customer)
                        <span class="customer-assigned">
                            {{ $onu->customer_names }}
                        </span>
                        @if($onu->customerAssignments->count() > 1)
                            <br><small class="customer-multiple">
                                ({{ $onu->customerAssignments->count() }} clientes)
                            </small>
                        @endif
                    @else
                        <span class="customer-not-assigned">Sin asignar</span>
                    @endif
                </td>
                <td>
                    @if($onu->registered_at)
                        {{ $onu->registered_at->format('d/m/Y') }}
                    @else
                        <span class="customer-not-assigned">N/A</span>
                    @endif
                </td>
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
            Este documento contiene información técnica de la infraestructura de red.
        </p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 25;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>