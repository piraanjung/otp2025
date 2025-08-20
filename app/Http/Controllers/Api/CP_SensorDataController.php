<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IotCompost\CP_SensorData;
use Illuminate\Http\Request;

class CP_SensorDataController extends Controller
{
    /**
     * Store a new sensor data entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming data from the ESP32
        $request->validate([
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'methane_gas' => 'required|numeric',
            'weight' => 'required|numeric',
        ]);

        // Create a new SensorData record
        CP_SensorData::create([
            'temperature' => $request->temperature,
            'humidity' => $request->humidity,
            'methane_gas' => $request->methane_gas,
            'weight' => $request->weight,
        ]);

        return response()->json([
            'message' => 'Sensor data received successfully!'
        ], 201);
    }
}