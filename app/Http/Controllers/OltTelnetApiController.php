<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OltTelnetApiController extends Controller
{
    public function systemStatus($oltId = null)
    {
        return response()->json(['success' => true, 'message' => 'stub systemStatus']);
    }

    public function listOnus($oltId = null)
    {
        return response()->json(['success' => true, 'message' => 'stub listOnus']);
    }

    public function addOnu(Request $request, $oltId = null)
    {
        return response()->json(['success' => true, 'message' => 'stub addOnu']);
    }

    public function getAlarms($oltId = null)
    {
        return response()->json(['success' => true, 'message' => 'stub getAlarms']);
    }

    public function createVlan(Request $request, $oltId = null)
    {
        return response()->json(['success' => true, 'message' => 'stub createVlan']);
    }

    public function runCommand(Request $request, $oltId = null)
    {
        return response()->json(['success' => true, 'message' => 'stub runCommand']);
    }
}
