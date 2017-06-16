<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Album;
use App\Http\Controllers\Controller;
use View;
use Auth;

class GalleryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        View::share('title', 'Hemali Shah | Gallery');
        View::share('page_title', 'Photo Gallery');
        View::share('gallery_active_class', 'active open');
        View::share('gallery_active_span', '<span class="selected"></span><span class="arrow open"></span>');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = Album::orderBy('display_order')
                        ->orderBy('created_at')
                        ->get();
        $imagesList = '';
        if(isset($albums[0])) {
            $images = $albums[0]->getMedia();
            $index = 0;
            foreach ($images as $image) {
                $imagesList[$index]['id'] = $image->id;
                $imagesList[$index]['title'] = $image->title;
                $imagesList[$index]['thumb'] = $image->getUrl('thumb');
                $imagesList[$index]['image'] = $image->getUrl();
                $imagesList[$index]['order'] = $image->display_order;
                $index++;
            }
        }
        
        if($imagesList != '') {
            usort($imagesList, function($a, $b)
            {
              return $a['order'] - $b['order'];
            });
        }
        
        return view('admin.gallery.index', compact('albums', 'imagesList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $albums = Album::orderBy('display_order')
                        ->orderBy('created_at')
                        ->get();
        return view('admin.gallery.create')->with('albums', $albums);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $album['title'] = $data['aboutus_head_title'];
        $album['created_by'] = Auth::user()->id;
        $album['updated_by'] = Auth::user()->id;

        if ($result = Album::create($album)) {
            if($request->image){
                $media = $result->addMedia($request->file('image'))
                        ->preservingOriginal()
                        ->toMediaLibrary('Albums');
            }
            \Session::flash('success','Album successfully added.');
        } else {
            \Session::flash('warning','Album not added.');
        }

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $album = Album::find($id);
        $imagesList = '';
        $images = $album->getMedia();
        foreach ($images as $key => $image) {
            $imagesList[$key]['id'] = $image->id;
            $imagesList[$key]['title'] = $image->title;
            $imagesList[$key]['thumb'] = $image->getUrl('thumb');
            $imagesList[$key]['image'] = $image->getUrl();
            $imagesList[$key]['order'] = $image->display_order;
        }

        if($imagesList != '') {

            usort($imagesList, function($a, $b)
            {
              return $a['order'] - $b['order'];
            });
            
        }

        if(isset($request->all()['option']))
            return view('admin.gallery.images-table2')->with('imagesList', $imagesList);
        else
            return view('admin.gallery.images-table')->with('imagesList', $imagesList);
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
    public function updateorder($id)
    {
        $response = ['status' => false, 'message' => 'Album order not updated.'];

        $ids = explode("|", $id);
        if(trim($ids[count($ids) - 1])== ''){
            unset($ids[count($ids) - 1]);
        }

        foreach ($ids as $key => $id) {

            $data['display_order'] = $key + 1;
            if ($result = Album::where('id', $id)->update($data))
                $response = ['status' => true, 'message' => 'Album order successfully updated.'];
            else {
                $response = ['status' => false, 'message' => 'Album order not updated'];
                break;
            }
            unset($data);

        }

        return response()->json($response);
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
    public function destroy(Request $reuest, $id)
    {
        $response = ['status' => false, 'message' => 'Album(s) not deleted'];
        $ids = explode("|", $id);
        if(count($ids) == 1)
        {
            if($result = Album::where('id', $id)->delete())
                $response = ['status' => true, 'message' => 'Album successfully deleted'];
            else
                $response = ['status' => false, 'message' => 'Album not deleted'];
        } else {
            if(trim($ids[count($ids) - 1])== '')
                unset($ids[count($ids) - 1]);

            if($result = Album::whereIn('id', $ids)->delete())
                $response = ['status' => true, 'message' => 'Album(s) successfully deleted.'];
            else
                $response = ['status' => false, 'message' => 'Album(s) not deleted'];
        }

        return response()->json($response);
    }
}
