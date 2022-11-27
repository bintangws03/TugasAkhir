<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Prestasi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PrestasiController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permission:prestasi.index|prestasi.create|prestasi.edit|prestasi.delete']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prestasis = Prestasi::latest()->when(request()->q, function($prestasis) {
            $prestasis = $prestasis->where('title', 'like', '%'. request()->q . '%');
        })->paginate(10);

        return view('admin.prestasi.index', compact('prestasis'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tags = Tag::latest()->get();
        $categories = Category::latest()->get();
        return view('admin.prestasi.create', compact('tags', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'         => 'required|unique:prestasis',
            'category_id'   => 'required',
            'content'       => 'required',
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/prestasis', $image->hashName());

        $prestasi = Prestasi::create([
            'image'       => $image->hashName(),    
            'title'       => $request->input('title'),
            'slug'        => Str::slug($request->input('title'), '-'),
            'category_id' => $request->input('category_id'),
            'content'     => $request->input('content')  
        ]);

        //assign tags
        $prestasi->tags()->attach($request->input('tags'));
        $prestasi->save();

        if($prestasi){
            //redirect dengan pesan sukses
            return redirect()->route('admin.prestasi.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.prestasi.index')->with(['error' => 'Data Gagal Disimpan!']);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Prestasi $prestasi)
    {
        $tags = Tag::latest()->get();
        $categories = Category::latest()->get();
        return view('admin.prestasi.edit', compact('prestasi', 'tags', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Prestasi $prestasi)
    {
        $this->validate($request,[
            'title'         => 'required|unique:prestasis,title,'.$prestasi->id,
            'category_id'   => 'required',
            'content'       => 'required',
        ]);

        if ($request->file('image') == "") {
        
            $prestasi = Prestasi::findOrFail($prestasi->id);
            $prestasi->update([
                'title'       => $request->input('title'),
                'slug'        => Str::slug($request->input('title'), '-'),
                'category_id' => $request->input('category_id'),
                'content'     => $request->input('content')  
            ]);

        } else {

            //remove old image
            Storage::disk('local')->delete('public/prestasis/'.$prestasi->image);

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/prestasis', $image->hashName());

            $prestasi = Prestasi::findOrFail($prestasi->id);
            $prestasi->update([
                'image'       => $image->hashName(),
                'title'       => $request->input('title'),
                'slug'        => Str::slug($request->input('title'), '-'),
                'category_id' => $request->input('category_id'),
                'content'     => $request->input('content')  
            ]);

        }

        //assign tags
        $prestasi->tags()->sync($request->input('tags'));

        if($prestasi){
            //redirect dengan pesan sukses
            return redirect()->route('admin.prestasi.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.prestasi.index')->with(['error' => 'Data Gagal Diupdate!']);
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
        $prestasi = Prestasi::findOrFail($id);
        $image = Storage::disk('local')->delete('public/prestasis/'.$prestasi->image);
        $prestasi->delete();

        if($prestasi){
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