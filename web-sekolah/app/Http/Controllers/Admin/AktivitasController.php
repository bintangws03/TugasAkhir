<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AktivitasController extends Controller
{
    public function index()
    {
        $aktivitass = ActivityLog::latest()->when(request()->q, function($aktivitass) {
            $aktivitass = $aktivitass->where('title', 'like', '%'. request()->q . '%');
        })->paginate(10);
        $activity_log = ActivityLog::with('user')->limit(10)->orderBy('id', 'DESC')->get();
        return view('admin.aktivitas.index', compact('posts'));
    }
}
