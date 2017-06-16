<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Aboutus;
use App\Models\Service;
use App\Models\Media;
use App\Models\Album;
use App\Models\Blog;

class MediaController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    	$section = strtolower($request->all()['section']);
    	
    	if($section == 'aboutus'){
        	$tableRecord = Aboutus::find($this->siteId);
        }
        else if($section == 'services'){
            $id = strtolower($request->all()['section_id']);
            $tableRecord = Service::where('id', $id)->get();
        }
        else if($section == 'blogs'){
            $id = strtolower($request->all()['section_id']);
            $tableRecord = Blog::where('id', $id)->get();
        }
        else if($section == 'gallery'){
            $id = strtolower($request->all()['section_id']);
            $tableRecord = Album::where('id', $id)->get();
        }        
       	else 
       		return;

        if(isset($tableRecord[0]))
            $tableRecord = $tableRecord[0];

        if($request->file_data){
            $media = $tableRecord->addMedia($request->file('file_data'))
                    ->preservingOriginal()
                    ->toMediaLibrary(ucfirst($section));
        }
        return;
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
        $response = ['status' => false, 'message' => 'Images order not updated.'];

        $ids = explode("|", $id);
        if(trim($ids[count($ids) - 1])== ''){
            unset($ids[count($ids) - 1]);
        }

        foreach ($ids as $key => $id) {

            $data['display_order'] = $key + 1;
            if ($result = Media::where('id', $id)->update($data))
                $response = ['status' => true, 'message' => 'Images order successfully updated.'];
            else {
                $response = ['status' => false, 'message' => 'Images order not updated'];
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
        $result = Media::where('id', $id)->update(['title' => $request->all()['title']]);
        if ($result) {
            \Session::flash('success','Successfully updated.');
        } else {
            \Session::flash('warning','Not updated.');
        }
        return;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $response = ['status' => false, 'message' => 'Image(s) not deleted'];

        $_section = strtolower($request->all()['_section']);

        if($_section == 'aboutus'){
            $tableRecord = Aboutus::find($this->siteId);
        } else if($_section == 'services'){
            $section_id = strtolower($request->all()['_section_id']);
            $tableRecord = Service::where('id', $section_id)->get();
        } else if($_section == 'blogs'){
            $section_id = strtolower($request->all()['_section_id']);
            $tableRecord = Blog::where('id', $section_id)->get();
        } else if($_section == 'gallery'){
            $section_id = strtolower($request->all()['_section_id']);
            $tableRecord = Album::where('id', $section_id)->get();
        }
        else 
            return;

        if(isset($tableRecord[0]))
            $tableRecord = $tableRecord[0];

        $images = $tableRecord->getMedia();
        $ids = explode("|", $id);

        foreach ($images as $image) {
            if(in_array($image->id, $ids)){
                $result = $image->delete();
            }
        }

        if($result)
            $response = ['status' => true, 'message' => 'Image(s) successfully deleted.'];
        else
            $response = ['status' => false, 'message' => 'Image(s) not deleted'];            

        return response()->json($response);
    }
}
