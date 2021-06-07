<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
    * index
    *
    * @param  mixed $request
    * @return void
    */
    public function index()
    {
        $posts = Post::latest()->when(request()->search, function($posts) {
            $posts = $posts->where('nama', 'like', '%'. request()->search . '%');
        })->paginate(5);

        return view('post.index', compact('posts'));
    }
    /**
    * create
    *
    * @param  mixed $request
    * @return void
    */
    public function create()
    {
        return view('post.create');
    } 
    /**
    * store
    *
    * @param  mixed $request
    * @return void
    */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nama'     => 'required',
            'alamat'     => 'required',
            'kontak'   => 'required',
            'kapasitas' => 'required'
        ]);

        $post = Post::create([
            'nama'     => $request->nama,
            'alamat'    => $request->alamat,
            'kontak'   => $request->kontak,
            'kapasitas' => $request->kapasitas
        ]);

        if($post){
            //redirect dengan pesan sukses
            return redirect()->route('post.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('post.index')->with(['error' => 'Data Gagal Disimpan!']);
        }

    }
    /**
    * edit
    *
    * @param  mixed $request
    * @return void
    */
    public function edit(Post $post)
    {
        return view('post.edit', compact('post'));
    }
        
    /**
    * update
    *
    * @param  mixed $request
    * @param  mixed $post
    * @return void
    */
    public function update(Request $request, Post $post)
    {
        $this->validate($request, [
                'nama'     => 'required',
                'alamat'   => 'required',
                'kontak'   => 'required',
                'kapasitas' => 'required'
        ]);
            
        //get data post by ID
        $post = Post::findOrFail($post->id);
            
        if($request->file('image') == "") {
            
            $post->update([
                'nama'     => $request->nama,
                'alamat'    => $request->alamat,
                'kontak'   => $request->kontak,
                'kapasitas' => $request->kapasitas
            ]);
            
        } else {
            
            //hapus old image
            Storage::disk('local')->delete('public/posts/'.$post->image);
            
            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            
            $post->update([
                'nama'     => $request->nama,
                'alamat'    => $request->alamat,
                'kontak'   => $request->kontak,
                'kapasitas' => $request->kapasitas
            ]);
            
        }
            
        if($post){
            //redirect dengan pesan sukses
            return redirect()->route('post.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('post.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }
    /**
    * destroy
    *
    * @param  mixed $request
    * @return void
    */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        Storage::disk('local')->delete('public/posts/'.$post->image);
        $post->delete();

        if($post){
            //redirect dengan pesan sukses
            return redirect()->route('post.index')->with(['success' => 'Data Berhasil Dihapus!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('post.index')->with(['error' => 'Data Gagal Dihapus!']);
        }
    }
}
