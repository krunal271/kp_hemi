<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Video;
use App\Http\Controllers\Controller;
use View;
use Auth;

class VideoController extends Controller
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
        View::share('page_title', 'Video Gallery');
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
        $videos = Video::orderBy('display_order')
                            ->orderBy('created_at')
                            ->get();
        return view('admin.video.index')->with('videos', $videos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function updateorder($id)
    {
        $response = ['status' => false, 'message' => 'Videos order not updated.'];

        $ids = explode("|", $id);
        if(trim($ids[count($ids) - 1])== ''){
            unset($ids[count($ids) - 1]);
        }

        foreach ($ids as $key => $id) {

            $data['display_order'] = $key + 1;
            if ($result = Video::where('id', $id)->update($data))
                $response = ['status' => true, 'message' => 'Videos order successfully updated.'];
            else {
                $response = ['status' => false, 'message' => 'Videos order not updated'];
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
        $response = ['status' => false, 'message' => 'Video not updated'];

        $data = $request->all();
        $result = Video::where('id', $id)->update($data);
        if ($result)
            $response = ['status' => true, 'message' => 'Video successfully updated.'];
        else
            $response = ['status' => false, 'message' => 'Video not updated'];            

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $reuest, $id)
    {
        $response = ['status' => false, 'message' => 'Video(s) not deleted'];
        $ids = explode("|", $id);
        if(count($ids) == 1)
        {
            if($result = Video::where('id', $id)->delete())
                $response = ['status' => true, 'message' => 'Video successfully deleted'];
            else
                $response = ['status' => false, 'message' => 'Video not deleted'];
        } else {
            if(trim($ids[count($ids) - 1])== '')
                unset($ids[count($ids) - 1]);

            if($result = Video::whereIn('id', $ids)->delete())
                $response = ['status' => true, 'message' => 'Video(s) successfully deleted.'];
            else
                $response = ['status' => false, 'message' => 'Video(s) not deleted'];
        }

        return response()->json($response);
    }
}
