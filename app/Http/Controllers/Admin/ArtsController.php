<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Art;
use App\Http\Controllers\Controller;
use View;
use Auth;

class ArtsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        View::share('title', 'Hemali Shah | Art');
        View::share('page_title', 'Art');
        View::share('arts_active_class', 'active open');
        View::share('arts_active_span', '<span class="selected"></span><span class="arrow open"></span>');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arts = Art::orderBy('display_order')->get();
        foreach ($arts as $art) {
            $artImage = $art->getMedia();
            if(isset($artImage[0])){
                $art['thumb'] = $artImage[0]->getUrl('thumb');
                $art['image'] = $artImage[0]->getUrl();
            }
        }
        return view('admin.arts.index')->with('arts', $arts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.arts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $art = $request->only(['title', 'link', 'target']);
        $art['created_by'] = Auth::user()->id;
        $art['updated_by'] = Auth::user()->id;

        if ($result = Art::create($art)) {
            if($request->image){
                $media = $result->addMedia($request->file('image'))
                        ->preservingOriginal()
                        ->toMediaLibrary('Arts');
            }
            \Session::flash('success','Art successfully added.');
        } else {
            \Session::flash('warning','Art not added.');
        }
        return redirect('/admin/arts');
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
        $method = 'PUT';
        $art = Art::find($id);
        $artImage = $art->getMedia();
        if(isset($artImage[0])){
            $art['image'] = $artImage[0]->getUrl('admin');
        }
        return view('admin.arts.edit', compact('art', 'method'));
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
        $response = ['status' => false, 'message' => 'Arts order not updated.'];

        $ids = explode("|", $id);
        if(trim($ids[count($ids) - 1])== ''){
            unset($ids[count($ids) - 1]);
        }

        foreach ($ids as $key => $id) {

            $data['display_order'] = $key + 1;
            if ($result = Art::where('id', $id)->update($data))
                $response = ['status' => true, 'message' => 'Arts order successfully updated.'];
            else {
                $response = ['status' => false, 'message' => 'Arts order not updated'];
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
        $art = $request->only(['title', 'link', 'target']);
        $art['updated_by'] = Auth::user()->id;

        if ($result = Art::where('id', $id)->update($art)) {
            if($request->image){
                $art = Art::find($id);
                $art->clearMediaCollection('Arts');
                $result = $art->addMedia($request->file('image'))
                         ->preservingOriginal()
                         ->toMediaLibrary('Arts');
            }
            \Session::flash('success','Art successfully updated.');
        } else {
            \Session::flash('warning','Art not updated.');
        }
        return redirect('/admin/arts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = ['status' => false, 'message' => 'Arts not deleted'];
        $ids = explode("|", $id);
        if(count($ids) == 1)
        {
            if($result = Art::where('id', $id)->delete())
                $response = ['status' => true, 'message' => 'Art successfully deleted'];
            else
                $response = ['status' => false, 'message' => 'Art not deleted'];
        } else {
            if(trim($ids[count($ids) - 1])== '')
                unset($ids[count($ids) - 1]);

            if($result = Art::whereIn('id', $ids)->delete())
                $response = ['status' => true, 'message' => 'Arts successfully deleted.'];
            else
                $response = ['status' => false, 'message' => 'Arts not deleted'];
        }
        return response()->json($response);
    }
}
