<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\User;
use App\Http\Controllers\Controller;
use View;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        View::share('title', 'Hemali Shah | Team Members');
        View::share('page_title', 'Team Members');
        View::share('users_active_class', 'active open');
        View::share('users_active_span', '<span class="selected"></span><span class="arrow open"></span>');
        $this->siteId = 1;        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('role', 'TeamMember')
                        ->orderBy('display_order')
                        ->orderBy('created_at')
                        ->get();

        foreach ($users as $user) {
            $userImage = $user->getMedia();
            if(isset($userImage[0])){
                $user['thumb'] = $userImage[0]->getUrl('thumb');
                $user['image'] = $userImage[0]->getUrl();
            }
        }
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
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
        $method = 'PUT';
        $user = User::find($id);
        $userImage = $user->getMedia();
        if(isset($userImage[0])){
            $user['image'] = $userImage[0]->getUrl();
        }
        return view('admin.users.edit', compact('user', 'method'));
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
        $response = ['status' => false, 'message' => 'Team members order not updated.'];

        $ids = explode("|", $id);
        if(trim($ids[count($ids) - 1])== ''){
            unset($ids[count($ids) - 1]);
        }

        foreach ($ids as $key => $id) {

            $data['display_order'] = $key + 1;
            if ($result = User::where('id', $id)->update($data))
                $response = ['status' => true, 'message' => 'Team members order successfully updated.'];
            else {
                $response = ['status' => false, 'message' => 'Team members order not updated'];
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
        $data = $request->except(['_method', '_token', 'image']);

        if ($result = User::where('id', $id)->update($data)) {
            if($request->image){
                $user = User::find($id);

                $user->clearMediaCollection('User');
                $result = $user->addMedia($request->file('image'))
                         ->preservingOriginal()
                         ->toMediaLibrary('User');

            }
            \Session::flash('success','Team member successfully updated.');
        } else {
            \Session::flash('warning','Team member not updated.');
        }

        return redirect('/admin/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = ['status' => false, 'message' => 'Team member(s) not deleted'];
        $ids = explode("|", $id);
        if(count($ids) == 1)
        {
            if($result = User::where('id', $id)->delete())
                $response = ['status' => true, 'message' => 'Team member successfully deleted'];
            else
                $response = ['status' => false, 'message' => 'Team member not deleted'];
        } else {
            if(trim($ids[count($ids) - 1])== '')
                unset($ids[count($ids) - 1]);

            if($result = User::whereIn('id', $ids)->delete())
                $response = ['status' => true, 'message' => 'Team member(s) successfully deleted.'];
            else
                $response = ['status' => false, 'message' => 'Team member(s) not deleted'];
        }
        
        return response()->json($response);
    }

    /**
     * Users profile
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile($id)
    {
        View::share('title', 'Showcase | Profile');
        View::share('page_title', 'Profile');        
        return view('admin.users.profile');
    }    
}
