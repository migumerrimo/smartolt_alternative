<?php

namespace App\Console\Commands;

use App\Models\Alarm;
use App\Models\Olt;
use App\Models\Onu;
use Illuminate\Console\Command;

class SimulateAlarms extends Command
{
    protected $signature = 'alarms:simulate 
                            {--scenario=normal : Escenario (normal, crisis, maintenance)}
                            {--clear : Limpiar todas las alertas de simulación primero}';
    
    protected $description = 'Simula alertas del sistema OLT/ONU para testing';

    // Escenarios predefinidos
    private $scenarios = [
        'normal' => [
            'critical' => ['count' => 0, 'chance' => 5],
            'major' => ['count' => 2, 'chance' => 20],
            'minor' => ['count' => 5, 'chance' => 40],
            'warning' => ['count' => 3, 'chance' => 60],
            'info' => ['count' => 3, 'chance' => 60]
        ],
        'crisis' => [
            'critical' => ['count' => 3, 'chance' => 30],
            'major' => ['count' => 8, 'chance' => 50],
            'minor' => ['count' => 15, 'chance' => 70],
            'warning' => ['count' => 5, 'chance' => 40],
            'info' => ['count' => 5, 'chance' => 40]
        ],
        'maintenance' => [
            'critical' => ['count' => 1, 'chance' => 10],
            'major' => ['count' => 4, 'chance' => 30],
            'minor' => ['count' => 10, 'chance' => 60],
            'warning' => ['count' => 8, 'chance' => 80],
            'info' => ['count' => 8, 'chance' => 80]
        ]
    ];

    public function handle()
    {
        $scenario = $this->option('scenario');
        
        if (!array_key_exists($scenario, $this->scenarios)) {
            $this->error("❌ Escenario no válido: $scenario");
            $this->info("🎭 Escenarios disponibles: normal, crisis, maintenance");
            return 1;
        }

        $this->info("🏗️  Iniciando simulación de alertas - Escenario: " . strtoupper($scenario));
        
        // Limpiar alertas anteriores si se solicita
        if ($this->option('clear')) {
            $this->clearSimulationAlarms();
        }
        
        // Ejecutar simulación
        $this->simulateAlarms($scenario);
        
        $this->info("✅ Simulación completada!");
        $this->showSummary();
        
        return 0;
    }

    private function clearSimulationAlarms()
    {
        $deleted = Alarm::where('message', 'LIKE', '%[SIM]%')->delete();
        $this->info("🗑️  Eliminadas $deleted alertas de simulación anteriores");
    }

    private function simulateAlarms($scenario)
    {
        $this->info("📊 Generando alertas para escenario: " . strtoupper($scenario));
        
        $scenarioConfig = $this->scenarios[$scenario];
        
        // Generar alertas para cada nivel de severidad
        foreach ($scenarioConfig as $severity => $config) {
            $this->generateAlarmsForSeverity($severity, $config['count']);
        }
    }

    private function generateAlarmsForSeverity($severity, $count)
    {
        if ($count === 0) {
            return;
        }

        $this->info("  📍 Generando $count alertas de tipo: $severity");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $this->createRandomAlarm($severity);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function createRandomAlarm($severity)
    {
        $olts = Olt::all();
        $onus = Onu::all();
        
        if ($olts->isEmpty()) {
            $this->warn("⚠️  No hay OLTs en la base de datos. Ejecuta los seeders primero.");
            return;
        }

        $olt = $olts->random();
        $onu = $onus->isNotEmpty() ? $onus->random() : null;
        
        // Tipos de alertas por severidad
        $alarmTemplates = $this->getAlarmTemplates($severity);
        $template = $alarmTemplates[array_rand($alarmTemplates)];
        
        // Reemplazar placeholders
        $message = str_replace(
            ['{OLT_NAME}', '{ONU_SERIAL}', '{OLT_IP}', '{ONU_MODEL}', '{PON_PORT}'],
            [
                $olt->name, 
                $onu ? $onu->serial_number : 'ONU-UNKNOWN',
                $olt->management_ip, 
                $onu ? $onu->model : 'MODEL-UNKNOWN',
                $onu ? $onu->pon_port : '1/1/' . rand(1, 8)
            ],
            $template
        );

        // Decidir si es alerta de OLT o ONU (las críticas suelen ser de OLT)
        $isOnuAlarm = $onu && rand(0, 1) && $severity !== 'critical';

        Alarm::create([
            'olt_id' => $olt->id,
            'onu_id' => $isOnuAlarm ? $onu->id : null,
            'severity' => $severity,
            'message' => $message . ' [SIM]',
            'active' => true,
            'detected_at' => now()->subMinutes(rand(1, 240)) // Alertas de 1 min a 4 horas
        ]);
    }

    private function getAlarmTemplates($severity)
    {
        return match($severity) {
            'critical' => [
                "🚨 {OLT_NAME} - NO RESPONDE - Fuera de línea desde " . rand(5, 30) . "min",
                "💥 PÉRDIDA MASIVA - " . rand(60, 95) . "% ONUs desconectadas en {OLT_NAME}",
                "🔴 SWITCH CORE - Puerto uplink caído en {OLT_NAME}",
                "⚡ FALLA ELÉCTRICA - {OLT_NAME} sin alimentación",
                "🌐 BGP SESSION DOWN - {OLT_NAME} desconectado del core"
            ],
            'major' => [
                "⚠️  {ONU_SERIAL} - Señal crítica: -" . rand(285, 320)/10 . "dBm en {OLT_NAME}",
                "📈 ALTA LATENCIA - {OLT_NAME} > " . rand(150, 500) . "ms hacia core",
                "🔥 CPU ALTO - {OLT_NAME} en " . rand(85, 98) . "% por " . rand(10, 30) . "min",
                "💾 MEMORIA - {OLT_NAME} con " . rand(85, 95) . "% de uso",
                "📡 {ONU_SERIAL} - OFFLINE por " . rand(15, 120) . " minutos"
            ],
            'minor' => [
                "🔧 {ONU_SERIAL} - " . rand(3, 15) . " reconexiones en 1h",
                "🌡️  TEMPERATURA - {OLT_NAME}: " . rand(65, 75) . "°C",
                "📊 INTERFACE ERRORS - Puerto {PON_PORT} en {OLT_NAME}",
                "🔄 {ONU_SERIAL} - Resincronización PON cada " . rand(5, 20) . "min",
                "📶 {ONU_SERIAL} - Señal baja: -" . rand(265, 280)/10 . "dBm"
            ],
            'warning' => [
                "📢 {OLT_NAME} - Backup automático pendiente",
                "🛠️  {ONU_SERIAL} - Firmware desactualizado",
                "📋 MANTENIMIENTO - Ventana programada para {OLT_NAME}",
                "🔔 {OLT_NAME} - Logs cerca del límite (" . rand(75, 90) . "%)",
                "📊 {ONU_SERIAL} - Alto uso de bandwidth (" . rand(70, 85) . "Mbps)"
            ],
            'info' => [
                "ℹ️  {ONU_SERIAL} - Nueva ONU registrada en {OLT_NAME}",
                "📋 MANTENIMIENTO - Actualización completada en {OLT_NAME}",
                "✅ {ONU_SERIAL} - Signal restored: -" . rand(220, 250)/10 . "dBm",
                "🔔 MONITOREO - Revisión periódica completada en {OLT_NAME}",
                "📈 {OLT_NAME} - Performance estable última hora"
            ],
            default => []
        };
    }

    private function showSummary()
    {
        $summary = Alarm::where('message', 'LIKE', '%[SIM]%')
                       ->selectRaw('severity, count(*) as count')
                       ->groupBy('severity')
                       ->pluck('count', 'severity')
                       ->toArray();

        $this->info("\n📈 RESUMEN DE ALERTAS GENERADAS:");
        $this->info("=================================");
        
        foreach (['critical', 'major', 'minor', 'warning', 'info'] as $severity) {
            $count = $summary[$severity] ?? 0;
            $icon = match($severity) {
                'critical' => '🔴',
                'major' => '🟠', 
                'minor' => '🔵',
                'warning' => '🟡',
                'info' => '⚪'
            };
            
            $this->info("  $icon " . str_pad(ucfirst($severity), 10) . ": $count alertas");
        }
        
        $total = array_sum($summary);
        $this->info("=================================");
        $this->info("   📊 TOTAL: $total alertas activas");
        
        $this->info("\n🌐 Para ver las alertas: Visita /alarms en tu navegador");
    }
}