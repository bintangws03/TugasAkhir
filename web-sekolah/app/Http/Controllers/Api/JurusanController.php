<?php

namespace App\Http\Controllers\Api;

use App\Models\Jurusan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
   /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $jurusans = Jurusan::latest()->paginate(6);
        return response()->json([
            "response" => [
                "status"    => 200,
                "message"   => "List Data Jurusan"
            ],
            "data" => $jurusans
        ], 200);
    } 
}
