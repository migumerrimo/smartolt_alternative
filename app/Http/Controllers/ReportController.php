<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Olt;
use App\Models\Onu;
use App\Models\Alarm;
use PDF;

class ReportController extends Controller
{
    /**
     * Generar reporte diario básico
     */
    public function dailyReport()
    {
        // Datos para el reporte
        $reportData = $this->getDailyReportData();
        
        // Generar PDF
        $pdf = PDF::loadView('reports.daily', $reportData);
        
        // Descargar el PDF
        return $pdf->download('reporte-diario-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Vista previa del reporte (HTML)
     */
    public function previewDailyReport()
    {
        $reportData = $this->getDailyReportData();
        return view('reports.daily', $reportData);
    }
    
    /**
     * Obtener datos para el reporte diario
     */
    private function getDailyReportData()
    {
        // Datos básicos del sistema
        $oltsTotal = Olt::count();
        $oltsActive = Olt::where('status', 'active')->count();
        $onusTotal = Onu::count();
        $onusOnline = Onu::where('status', 'online')->count();
        
        // Alertas
        $criticalAlarms = Alarm::where('severity', 'critical')
                              ->where('active', true)
                              ->count();
        $majorAlarms = Alarm::where('severity', 'major')
                           ->where('active', true)
                           ->count();
        
        // OLTs principales (las 5 más recientes o con más ONUs)
        $mainOlts = Olt::withCount(['onus as online_onus_count' => function($query) {
                        $query->where('status', 'online');
                    }])
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
        
        // Alertas recientes críticas/mayores
        $recentAlarms = Alarm::with(['olt', 'onu'])
                           ->whereIn('severity', ['critical', 'major'])
                           ->where('active', true)
                           ->orderBy('detected_at', 'desc')
                           ->take(10)
                           ->get();
        
        return [
            'reportDate' => now()->format('d/m/Y'),
            'generatedAt' => now()->format('d/m/Y H:i:s'),
            'oltsTotal' => $oltsTotal,
            'oltsActive' => $oltsActive,
            'onusTotal' => $onusTotal,
            'onusOnline' => $onusOnline,
            'criticalAlarms' => $criticalAlarms,
            'majorAlarms' => $majorAlarms,
            'mainOlts' => $mainOlts,
            'recentAlarms' => $recentAlarms,
            'healthScore' => $this->calculateHealthScore($oltsActive, $oltsTotal, $onusOnline, $onusTotal),
            'systemUptime' => '99.8%', // Por ahora estático
            'avgBandwidth' => '65%',   // Por ahora estático
            'avgLatency' => '24ms',    // Por ahora estático
        ];
    }
    
    /**
     * Calcular score de salud del sistema (simplificado)
     */
    private function calculateHealthScore($oltsActive, $oltsTotal, $onusOnline, $onusTotal)
    {
        if ($oltsTotal == 0 || $onusTotal == 0) return 100;
        
        $oltScore = ($oltsActive / $oltsTotal) * 100;
        $onuScore = ($onusOnline / $onusTotal) * 100;
        
        return round(($oltScore + $onuScore) / 2, 1);
    }
}