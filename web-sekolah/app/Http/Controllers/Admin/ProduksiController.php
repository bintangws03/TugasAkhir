<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produksi;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProduksiController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permission:produksi.index|produksi.create|produksi.edit|produksi.delete']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produksis = Produksi::latest()->when(request()->q, function($produksis) {
            $produksis = $produksis->where('title', 'like', '%'. request()->q . '%');
        })->paginate(10);

        return view('admin.produksi.index', compact('produksis'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $tags = Tag::latest()->get();
        $categories = Category::latest()->get();
        return view('admin.produksi.create', compact('categories'));
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
            'title'         => 'required|unique:produksis',
            'category_id'   => 'required',
            'content'       => 'required',
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/produksis', $image->hashName());

        $produksi = Produksi::create([
            'image'       => $image->hashName(),    
            'title'       => $request->input('title'),
            'slug'        => Str::slug($request->input('title'), '-'),
            'category_id' => $request->input('category_id'),
            'content'     => $request->input('content')  
        ]);

        if($produksi){
            //redirect dengan pesan sukses
            return redirect()->route('admin.produksi.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.produksi.index')->with(['error' => 'Data Gagal Disimpan!']);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Produksi $produksi)
    {
        // $tags = Tag::latest()->get();
        $categories = Category::latest()->get();
        return view('admin.produksi.edit', compact('produksi', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Produksi $produksi)
    {
        $this->validate($request,[
            'title'         => 'required|unique:produksis,title,'.$produksi->id,
            'category_id'   => 'required',
            'content'       => 'required',
        ]);

        if ($request->file('image') == "") {
        
            $produksi = Produksi::findOrFail($produksi->id);
            $produksi->update([
                'title'       => $request->input('title'),
                'slug'        => Str::slug($request->input('title'), '-'),
                'category_id' => $request->input('category_id'),
                'content'     => $request->input('content')  
            ]);

        } else {

            //remove old image
            Storage::disk('local')->delete('public/produksis/'.$produksi->image);

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/produksis', $image->hashName());

            $produksi = Produksi::findOrFail($produksi->id);
            $produksi ->update([
                'image'       => $image->hashName(),
                'title'       => $request->input('title'),
                'slug'        => Str::slug($request->input('title'), '-'),
                'category_id' => $request->input('category_id'),
                'content'     => $request->input('content')  
            ]);

        }

        //assign tags
        // $post->tags()->sync($request->input('tags'));

        if($produksi){
            //redirect dengan pesan sukses
            return redirect()->route('admin.produksi.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.produksi.index')->with(['error' => 'Data Gagal Diupdate!']);
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
        $produksi = Produksi::findOrFail($id);
        $image = Storage::disk('local')->delete('public/produksis/'.$produksi->image);
        $produksi->delete();

        if($produksi){
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