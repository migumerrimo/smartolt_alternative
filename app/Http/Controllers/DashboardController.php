<?php

namespace App\Http\Controllers;

use App\Models\Olt;
use App\Models\Onu;
use App\Models\Alarm;
use App\Models\Telemetry;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'olts_count'     => Olt::where('status','active')->count(),
            'onus_online'    => Onu::where('status','online')->count(),
            'alarms_active'  => Alarm::where('active',1)->count(),
            'telemetry_count'=> Telemetry::count(),
        ]);
    }
}
