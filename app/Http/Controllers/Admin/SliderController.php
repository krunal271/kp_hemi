<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Slider;
use App\Http\Controllers\Controller;
use View;
use Auth;

class SliderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        View::share('title', 'Hemali Shah | Slider');
        View::share('page_title', 'Slider');
        View::share('slider_active_class', 'active open');
        View::share('slider_active_span', '<span class="selected"></span><span class="arrow open"></span>');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = Slider::orderBy('display_order')
                            ->orderBy('created_at')
                            ->get();
                            
        foreach ($sliders as $slider) {
            $sliderImage = $slider->getMedia();
            if(isset($sliderImage[0])){
                $slider['thumb'] = $sliderImage[0]->getUrl('thumb');
                $slider['image'] = $sliderImage[0]->getUrl();
            }
        }
        return view('admin.slider.index')->with('sliders', $sliders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.slider.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $slider = $request->only(['title1','title2', 'link', 'target']);

        $slider['created_by'] = Auth::user()->id;
        $slider['updated_by'] = Auth::user()->id;

        if ($result = Slider::create($slider)) {
            if($request->image){
                $media = $result->addMedia($request->file('image'))
                        ->preservingOriginal()
                        ->toMediaLibrary('Slider');
            }
            \Session::flash('success','Slider successfully added.');
        } else {
            \Session::flash('warning','Slider not added.');
        }

        return redirect('/admin/slider');
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
        $slider = Slider::find($id);
        $sliderImage = $slider->getMedia();
        if(isset($sliderImage[0])){
            $slider['image'] = $sliderImage[0]->getUrl('admin');
        }
        return view('admin.slider.edit', compact('slider', 'method'));
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
        $response = ['status' => false, 'message' => 'Slider order not updated.'];

        $ids = explode("|", $id);
        if(trim($ids[count($ids) - 1])== ''){
            unset($ids[count($ids) - 1]);
        }

        foreach ($ids as $key => $id) {

            $data['display_order'] = $key + 1;
            if ($result = Slider::where('id', $id)->update($data))
                $response = ['status' => true, 'message' => 'Slider order successfully updated.'];
            else {
                $response = ['status' => false, 'message' => 'Slider order not updated'];
                break;
            }
            unset($data);

        }

        return response()->json($response);
    }

    /**
     * Update the resource display order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $slider = $request->only(['title1','title2', 'link', 'target']);
        $slider['updated_by'] = Auth::user()->id;

        if ($result = Slider::where('id', $id)->update($slider)) {
            if($request->image){
                $slider = Slider::find($id);
                $slider->clearMediaCollection('Slider');
                $result = $slider->addMedia($request->file('image'))
                         ->preservingOriginal()
                         ->toMediaLibrary('Slider');
            }
            \Session::flash('success','Slider successfully updated.');
        } else {
            \Session::flash('warning','Slider not updated.');
        }

        return redirect('/admin/slider');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $reuest, $id)
    {
        $response = ['status' => false, 'message' => 'Slider(s) not deleted'];
        $ids = explode("|", $id);
        if(count($ids) == 1)
        {
            if($result = Slider::where('id', $id)->delete())
                $response = ['status' => true, 'message' => 'Slider successfully deleted'];
            else
                $response = ['status' => false, 'message' => 'Slider not deleted'];
        } else {
            if(trim($ids[count($ids) - 1])== '')
                unset($ids[count($ids) - 1]);

            if($result = Slider::whereIn('id', $ids)->delete())
                $response = ['status' => true, 'message' => 'Slider(s) successfully deleted.'];
            else
                $response = ['status' => false, 'message' => 'Slider(s) not deleted'];
        }

        return response()->json($response);
    }
}
