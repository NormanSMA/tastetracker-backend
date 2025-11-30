<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * GET /api/areas
     * Retorna todas las áreas activas del restaurante
     */
    public function index()
    {
        $areas = Area::where('is_active', true)->get();

        return response()->json([
            'data' => $areas
        ]);
    }
}
