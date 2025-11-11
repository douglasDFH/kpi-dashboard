<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Personalizado - Metalúrgica Precision S.A.</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            background: #1e40af;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            opacity: 0.9;
        }
        .report-info {
            background: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .report-info p {
            margin-bottom: 5px;
        }
        .equipment-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .equipment-title {
            background: #3b82f6;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .metric-row {
            display: table-row;
        }
        .metric-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            background: white;
        }
        .metric-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        .metric-value.blue { color: #2563eb; }
        .metric-value.green { color: #16a34a; }
        .metric-value.orange { color: #ea580c; }
        .metric-value.purple { color: #9333ea; }
        .metric-value.red { color: #dc2626; }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            margin: 15px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            padding: 10px 0;
            border-top: 1px solid #e5e7eb;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Metalúrgica Precision S.A.</h1>
        <p>Reporte Personalizado</p>
    </div>

    <!-- Report Info -->
    <div class="report-info">
        <p><strong>Período:</strong> {{ $period['start'] }} - {{ $period['end'] }}</p>
        <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        <p><strong>Equipos incluidos:</strong> {{ count($data) }}</p>
        <p><strong>Métricas:</strong> {{ implode(', ', array_map('strtoupper', $selectedMetrics)) }}</p>
    </div>

    <!-- Equipment Data -->
    @foreach($data as $index => $equipmentData)
        <div class="equipment-section {{ $index < count($data) - 1 ? 'page-break' : '' }}">
            <div class="equipment-title">
                {{ $equipmentData['equipment']['name'] }} ({{ $equipmentData['equipment']['code'] }})
            </div>

            @if(isset($equipmentData['oee']))
                <div class="section-title">Indicadores OEE</div>
                <div class="metrics-grid">
                    <div class="metric-row">
                        <div class="metric-cell">
                            <div class="metric-label">OEE</div>
                            <div class="metric-value blue">{{ $equipmentData['oee']['oee'] }}%</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Disponibilidad</div>
                            <div class="metric-value green">{{ $equipmentData['oee']['availability'] }}%</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Rendimiento</div>
                            <div class="metric-value orange">{{ $equipmentData['oee']['performance'] }}%</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Calidad</div>
                            <div class="metric-value purple">{{ $equipmentData['oee']['quality'] }}%</div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($equipmentData['production']))
                <div class="section-title">Métricas de Producción</div>
                <div class="metrics-grid">
                    <div class="metric-row">
                        <div class="metric-cell">
                            <div class="metric-label">Planificado</div>
                            <div class="metric-value">{{ number_format($equipmentData['production']['total_planned']) }}</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Producido</div>
                            <div class="metric-value green">{{ number_format($equipmentData['production']['total_actual']) }}</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Unidades Buenas</div>
                            <div class="metric-value green">{{ number_format($equipmentData['production']['total_good']) }}</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Eficiencia</div>
                            <div class="metric-value blue">{{ number_format($equipmentData['production']['efficiency'], 1) }}%</div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($equipmentData['quality']))
                <div class="section-title">Métricas de Calidad</div>
                <div class="metrics-grid">
                    <div class="metric-row">
                        <div class="metric-cell">
                            <div class="metric-label">Inspeccionado</div>
                            <div class="metric-value">{{ number_format($equipmentData['quality']['total_inspected']) }}</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Aprobadas</div>
                            <div class="metric-value green">{{ number_format($equipmentData['quality']['total_approved']) }}</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Rechazadas</div>
                            <div class="metric-value red">{{ number_format($equipmentData['quality']['total_rejected']) }}</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Tasa de Calidad</div>
                            <div class="metric-value purple">{{ number_format($equipmentData['quality']['quality_rate'], 1) }}%</div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($equipmentData['downtime']))
                <div class="section-title">Tiempos Muertos</div>
                <div class="metrics-grid">
                    <div class="metric-row">
                        <div class="metric-cell">
                            <div class="metric-label">Total Minutos</div>
                            <div class="metric-value red">{{ number_format($equipmentData['downtime']['total_minutes']) }}</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Total Horas</div>
                            <div class="metric-value red">{{ $equipmentData['downtime']['total_hours'] }}</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">Planificado</div>
                            <div class="metric-value orange">{{ number_format($equipmentData['downtime']['planned']) }}</div>
                        </div>
                        <div class="metric-cell">
                            <div class="metric-label">No Planificado</div>
                            <div class="metric-value red">{{ number_format($equipmentData['downtime']['unplanned']) }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach

    <!-- Footer -->
    <div class="footer">
        <p>Documento generado automáticamente - Metalúrgica Precision S.A. © {{ date('Y') }}</p>
    </div>
</body>
</html>
