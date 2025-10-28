<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Clientes</title>
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
            padding: 10px 6px;
            text-align: left;
            font-weight: bold;
        }
        .table td {
            padding: 8px 6px;
            border-bottom: 1px solid #ddd;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge-residential {
            background: #007bff;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-business {
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-corporate {
            background: #ffc107;
            color: black;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
        }
        .onu-count {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
        }
        .onu-count-active {
            background: #d4edda;
            color: #155724;
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
        <h1>Reporte de Clientes del Sistema</h1>
        <div class="subtitle">
            Generado el: {{ $date }} | Sistema FreeOLT
        </div>
    </div>

    <!-- Resumen Estadístico -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-number">{{ $totalCustomers }}</div>
                <div class="summary-label">Total Clientes</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $residentialCustomers }}</div>
                <div class="summary-label">Residenciales</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $businessCustomers }}</div>
                <div class="summary-label">Empresariales</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $corporateCustomers }}</div>
                <div class="summary-label">Corporativos</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ $customersWithOnus }}</div>
                <div class="summary-label">Con ONUs</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Clientes -->
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Tipo</th>
                <th>Dirección</th>
                <th>ONUs Asignadas</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td><strong>{{ $customer->user->name }}</strong></td>
                <td>{{ $customer->user->email }}</td>
                <td>{{ $customer->user->phone ?? 'N/A' }}</td>
                <td>
                    @if($customer->customer_type === 'residential')
                        <span class="badge-residential">RESIDENCIAL</span>
                    @elseif($customer->customer_type === 'business')
                        <span class="badge-business">EMPRESARIAL</span>
                    @elseif($customer->customer_type === 'corporate')
                        <span class="badge-corporate">CORPORATIVO</span>
                    @else
                        <span>{{ $customer->customer_type }}</span>
                    @endif
                </td>
                <td>{{ $customer->address ?? 'N/A' }}</td>
                <td>
                    {{-- CORRECCIÓN: Usar assignedOnus en lugar de customerAssignments --}}
                    @if($customer->assignedOnus->count() > 0)
                        <span class="onu-count onu-count-active">
                            {{ $customer->assignedOnus->count() }} ONU(s)
                        </span>
                    @else
                        <span class="onu-count">Sin ONUs</span>
                    @endif
                </td>
                <td>
                    @if($customer->created_at)
                        {{ $customer->created_at->format('d/m/Y') }}
                    @else
                        N/A
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
            Este documento contiene información confidencial de clientes.
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