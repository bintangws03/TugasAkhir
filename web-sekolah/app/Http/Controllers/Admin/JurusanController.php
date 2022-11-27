<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permission:jurusan.index|jurusan.create|jursusan.edit|jurusan.delete']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jurusans = Jurusan::latest()->when(request()->q, function($jurusans) {
            $jurusans = $jurusans->where('name', 'like', '%'. request()->q . '%');
        })->paginate(10);

        return view('admin.jurusan.index', compact('jurusans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.jurusan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:jurusans'
        ]);

        $jurusan = Jurusan::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name'), '-') 
        ]);

        if($jurusan){
            //redirect dengan pesan sukses
            return redirect()->route('admin.jurusan.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.jurusan.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Jurusan $jurusan)
    {
        return view('admin.jurusan.edit', compact('jurusan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Jurusan $jurusan)
    {
        $this->validate($request, [
            'name' => 'required|unique:jurusans,name,'.$jurusan->id
        ]);

        $jurusan = Jurusan::findOrFail($jurusan->id);
        $jurusan ->update([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name'), '-') 
        ]);

        if($jurusan){
            //redirect dengan pesan sukses
            return redirect()->route('admin.jurusan.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.jurusan.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan ->delete();

        if($jurusan){
            return response()->json([
                'status' => 'success'
            ]);
        }else{
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}