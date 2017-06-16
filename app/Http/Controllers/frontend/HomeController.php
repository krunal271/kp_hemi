<?php

namespace App\Http\Controllers\frontend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\User;
use App\Models\Video;
use App\Models\Media;
use App\Models\Album;
use App\Models\Art;

class HomeController extends Controller
{
   public function index()
   {
  //for team member section
    $slider=Slider::all();
    $TeamMembers = User::where('role', 'TeamMember')->orderBy('display_order', 'asc')->take(3)->get();
    foreach ($TeamMembers as $TeamMember) {
        $userImage = $TeamMember->getMedia();
        if(isset($userImage[0])){
            $TeamMember['thumb'] = $userImage[0]->getUrl('thumb');
            $TeamMember['image'] = $userImage[0]->getUrl();
        }
    }
//for arts section
    $Arts=Art::all();
    $Arts=Art::orderBy('display_order','asc')->take(4)->get();
     foreach ($Arts as $art) {
            $artImage = $art->getMedia();
            if(isset($artImage[0])){
                $art['thumb'] = $artImage[0]->getUrl('thumb');
                $art['image'] = $artImage[0]->getUrl();
            }
        }
//for albums section
    $Albums=Album::orderBy('display_order','asc')->take(12)->get();
    foreach ($Albums as $album) {
            $images = $album->getMedia();
            $imagesList = '';
            $index = 0;
            foreach ($images as $image) {
                $imagesList[$index]['id'] = $image->id;
                $imagesList[$index]['title'] = $image->title;
                $imagesList[$index]['thumb'] = $image->getUrl('thumb');
                $imagesList[$index]['image'] = $image->getUrl();
                $imagesList[$index]['order'] = $image->display_order;
                $index++;
            }

            if($imagesList != '') {
                usort($imagesList, function($a, $b)
                {
                  return $a['order'] - $b['order'];
                });
            }

            $album['images'] = $imagesList;
    }
//for video section
  $Videos=Video::orderBy('display_order','asc')->take(4)->get();

//for slider section
  $slider=Slider::orderBy('display_order','asc')->take(4)->get();
  foreach ($slider as $sliderObj) {
        $userImage = $sliderObj->getMedia();
        if(isset($userImage[0])){
            $sliderObj['thumb'] = $userImage[0]->getUrl('thumb');
            $sliderObj['image'] = $userImage[0]->getUrl();
        }
    }

  $Media=Media::all();
    
  return view('frontend.index',compact('TeamMembers','Arts','Albums','Videos','slider'));
  }

  //for portfolio page
 public function portfolio()
   {
    $Albums=Album::orderBy('display_order','asc')->take(12)->get();
    foreach ($Albums as $album) {
            $images = $album->getMedia();
            $imagesList = '';
            $index = 0;
            foreach ($images as $image) {
                $imagesList[$index]['id'] = $image->id;
                $imagesList[$index]['title'] = $image->title;
                $imagesList[$index]['thumb'] = $image->getUrl('thumb');
                $imagesList[$index]['image'] = $image->getUrl();
                $imagesList[$index]['order'] = $image->display_order;
                $index++;
            }
                            
            if($imagesList != '') {
                usort($imagesList, function($a, $b)
                {
                  return $a['order'] - $b['order'];
                });
            }

            $album['images'] = $imagesList;
    }       

    $Videos=Video::orderBy('display_order','asc')->take(4)->get();

   	return view('frontend.portfolio',compact('Videos','Albums'));
   }

//for contact us section 
   public function contact(Request $request) 
   {    
        $data = $request->all();
        $emails = $data['email'];

        $mail['content']  = "Firstname: ".$data['fname']."<br>";
        $mail['content']  .= "Lastname: ".$data['lname']."<br>";
        $mail['content']  .= "Email: ".$data['email']."<br>";
        $mail['content']  .= "Mobile Number: ".$data['phone']."<br>";
        $mail['content']  .= "Comment: ".$data['comment']."<br>";
                 
        $result = \Mail::send('emails.contact_mail', ['mail' => $mail,'data'=>$data], function ($message) use ($mail,$data,$emails){
                $message->from($emails);
                $message->to(env('MAIL_TO_ADDRESS'));
                $message->from($emails,'HemikArts');
                $message->subject("Contact Us Page - New Message");
            });
        // if($result)
        // {
        //     return Response::json(['success' => 'true']);
        // }else{
        //     return Response::json(['success' => 'false']);
        // }
        return back();
    }

}
