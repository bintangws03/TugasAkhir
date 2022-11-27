<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $prestasis = Prestasi::latest()->paginate(6);
        return response()->json([
            "response" => [
                "status"    => 200,
                "message"   => "List Data Prestasi"
            ],
            "data" => $prestasis
        ], 200);
    }

}
