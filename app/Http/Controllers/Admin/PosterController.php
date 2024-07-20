<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosterImage;
use Illuminate\Http\Request;

class PosterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posters = PosterImage::paginate(10);
        // dd($posters);
        return view('admin.poster.index',compact('posters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.poster.create');
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
            'images' => 'required',
        ]);

        if($request->hasFile('images')){
                $image = $request->file('images');
                // dd($image);
                $fileName = rand().'_'.time() . '.' . $image->extension();

                $destinationPath = str_replace('\\', '/', base_path('public\assets\poster_images/'));
                $image->move($destinationPath, $fileName);

                $imageStore = PosterImage::create([
                    'images' => $fileName,
                ]);
        }
        return redirect()->route('poster.index')->with('success','Poster created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $poster = PosterImage::find($id)->delete();

        return redirect()->route('poster.index')->with('success','Poster deleted successfully');
    }
}
