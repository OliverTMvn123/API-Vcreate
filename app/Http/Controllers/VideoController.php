<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Video;
use App\Models\userinformation;
use App\Models\category;
use App\Models\comments;
use App\Models\likecmt;
use App\Models\LikeVideo;
use App\Models\shareVideo;
use App\Models\replycmt;
use App\Models\likeReplycmt;

use App\Http\Controllers\HomepageController;

class VideoController extends Controller
{
    public function index()
        {
            return 'Information  Video Api. 
            Can you show video = /show
            search video = /show/{id}';
        }
     public function hashfuc($item)
        {
            $tam=(string)$item;
            $re = hash('sha256', $item);
            return $re;
        }
    //  public function show1()
    //     {
    //         $data = Video::all();
    //         $all = [];
    //         foreach ($data as $video) {
    //             $likesCount = $video->likevideos()->count();
    //             $cocreation = $video->cocreation()->get();
    //             $user = UserInformation::find($video['idUser']);
    //             $homepageController = new HomepageController();
    //             $id1 = $this->hashfuc($video['id']); 
    //             $videoArray = $video->toArray();
    //             $videoArray['addressVideo']= $homepageController->getVideoSource($videoArray['adressVideo']);
    //             $videoArray['thumbNail'] = $homepageController->getImage($videoArray['thumbNail']);
    //             $videoArray['avatar'] = empty($user['avatar']) ? null : $homepageController->getImage($user['avatar']);
    //             $videoArray['idUser'] = $this->hashfuc($videoArray['idUser']);
    //             $videoArray['viewCount'] = 0;
    //             $videoArray['likeCount'] = $likesCount;
    //             $videoArray['shareCount'] = 0;
    //             $index = 0;
    //             if (!empty($cocreation)) {
    //                 foreach ($cocreation as $co) {
    //                     $nameUser = UserInformation::find($co['user_id']);
    //                     $videoArray['cocreate'.$index] = $nameUser ? $nameUser->name : null;
    //                     $index++;
    //                 }
    //             } else {
    //                 $videoArray['cocreate'] = null;
    //             }
    //             unset($videoArray['id']); 
    //             $newVideo = array_merge(['id1' => $id1], $videoArray);
                
    //             $all[] = $newVideo;
    //         }
            
    //         return response()->json($all);
    //     }
     
     
    // public function show($id)
    //     {
    //         $video = Video::find($id);
    //         $likesCount = $video->likevideos()->count();
    //         $cocreation = $video->cocreation()->get();
            
                
    //         $videoData = $video->toArray();

    //         $videoData['likesCount'] = $likesCount;
    //         $videoData['co-op'] = [];

    //         foreach ($cocreation as $item) {
    //             $nameUser = UserInformation::find($item['user_id']);
    //             $videoData['co-op'][] = [
    //                 'id' => $item['id'],
    //                 'name' => $nameUser->name,
    //                 'user_id' => $item['user_id'],
    //                 'video_id'=>$item['video_id'] 
    //                         ];
    //         }
            
    //         $data = [
    //             $videoData
    //         ];

    //         return response()->json($data);

    //     }
    
    // public function showUserVideo($id)
    //     {
    //         $videos = Video::where('idUser', $id)->get();
    //         $videodata = [];
            
    //         foreach ($videos as $video) {
    //             $array = [];
    //             $array['title'] = $video->titleVideo;
    //             $array['address'] = $video->addressVideo;
    //             $videodata[] = $array;
    //         }
            
        
            
    //         return response()->json($videodata);
    //     }
   
/////////////

    ////VIDEO PAGE
        public function videopage($id)
            {

                $videos = Video::all();
                if (!$videos) {
                    return response()->json(['error' => 'Video not found'], 404);
                }
                foreach($videos as $video)
                {
                    $sha250id= $this->hashfuc($video['id']);
                    if(hash_equals($id, $sha250id))
                    {
                        $likesCount = $video->likevideos()->count();

                        $videoData = $video->toArray();
                        $videoData['id'] = $this->hashfuc($videoData['id']); 

                        $functionController = new functionController();
                        $linkVideo = $functionController->getImage($videoData['thumbNail']);
                        $videoData['thumbNail'] = $linkVideo;
                        $videoData['adressVideo'] = $functionController->getVideoSource($videoData['adressVideo']);
                        
                        $user = UserInformation::find($videoData['idUser']);
                        $videoData['idUser'] = $this->hashfuc($videoData['idUser']); 
                        $videoData['likesCount'] = $likesCount;
                        $videoData['nameUser'] = $user ? $user->name : null;
                        $videoData['avatarUser']=empty($user['avatar']) ? null : $functionController->getImage($user['avatar']);
                        $a =DB::table('ratings')->where('user_id', $user->id)->avg('rating');
                        $videoData['userRating']= round($a, 1);
                        $data = [
                            $videoData
                        ];
                        return response()->json($data);
                    } 
                    
                }
                return response ()->json(['error' => 'Video not found'], 404);
            }
        public function showCoopcreation($id)
            {
                $videos = Video::all();
                if (!$videos) {
                    return response()->json(['error' => 'Video not found'], 404);
                }
                foreach($videos as $video)
                {
                    $sha250id= $this->hashfuc($video['id']);
                    if(hash_equals($id, $sha250id))
                    {
                    $cocreations = $video->cocreation()->get();
                    $data = [];
             
                    $functionController = new functionController();
                    foreach ($cocreations as $index => $cocreation) {
                        $cocreationData = []; 
                 
                        $cocreationData["video_id"] = $this->hashfuc($video->id); 
                        $cocreationData["user_id"] = $this->hashfuc($cocreation->user_id);
                        $userid = $cocreation->user_id;
                        $user = userinformation::find($userid);
                        if ($user) {
                            $cocreationData["user_name"] = $user->name;
                            $cocreationData["avatar"] = empty($user['avatar']) ? null : $functionController->getImage($user['avatar']);
                            $averageRating = DB::table('ratings')->where('user_id', $user->id)->avg('rating');
                            $cocreationData["userRating"] = round($averageRating, 1);
                        }
                
                        $data[$index] = $cocreationData; 
                    }
                    return response()->json($data);
                    }
                    
                }
                    return response()->json(['error' => 'Video not found'], 404);
            }
        public function showcmt($id)
        {
            $functionController =new functionController();
            $videos = Video::all();
            if (!$videos) {
                return response()->json(['error' => 'Video not found'], 404);
            }
            foreach($videos as $video)
            {
                $sha250id= $this->hashfuc($video['id']);
                if(hash_equals($id, $sha250id))
                {
                    $allcmt= comments::where('video_id',$video['id'])->get();
                    $data=[];
                    foreach ($allcmt as $cmt) {
                        $Arrcmt = $cmt->toArray();
                        $Arrcmt['id']=$this->hashfuc($cmt['id']);
                        $Arrcmt['user_id']=$this->hashfuc($cmt['user_id']);
                        $Arrcmt['video_id']=$this->hashfuc($cmt['video_id']);
                        $user = $functionController->getUserInfor($Arrcmt['user_id']);
                        $Arrcmt['username']=$user->name;
                        $Arrcmt['avatar']=$functionController->getImage($user->avatar);
                        $likesCount=likecmt::where('cmt_id',$cmt['id'])->count();
                        $Arrcmt['likesCount']=$likesCount;
                        unset($Arrcmt['updated_at']);
                        $replycmts= replycmt::where('cmt_id',$cmt['id'])->get();
                        $dataR=[];
                        foreach ($replycmts as $rcmt) {
                            $Arcmt = $rcmt->toArray();
                            $Arcmt['id']=$this->hashfuc($rcmt['id']);
                            $Arcmt['user_id']=$this->hashfuc($rcmt['user_id']);
                            $Arcmt['cmt_id']=$this->hashfuc($rcmt['cmt_id']);
                            $user = $functionController->getUserInfor($Arcmt['user_id']);
                            $Arcmt['username']=$user->name;
                            $Arcmt['avatar']=$functionController->getImage($user->avatar);
                            $likesCount=likeReplycmt::where('cmt_id',$rcmt['id'])->count();
                            $Arcmt['likesCount']=$likesCount;
                            unset($Arcmt['updated_at']);
                            $dataR[]=$Arcmt;
                        }
                        $Arrcmt['reply']=$dataR;
                        $data[]=$Arrcmt;
                    }
                    if(count($data)==0)
                    {
                        return null;
                    }
                    return response()->json($data);
                }
                
            }
                
            return response()->json(['error' => 'Video not found'], 404);
        }
      
        public function addview(Request $request)
        {
            if(isset($request['idVideo']))
                {
                    $functionController = new functionController();
                    $video = $functionController->getVideo($request['idVideo']);
                    if(!empty($video))
                    {
                        $video->view_count++; 
                        $video->save();
                        return true;
                    }
                    return false;
                }
        }
        public function CheckLikeVideo(Request $request)
        {
            $functionController = new functionController();
            if(isset($request['idVideo']) ){
                $getvideo=$functionController->getVideo($request['idVideo']);
               
                $all=[];
                if (!empty($getvideo)) {
                    $all['CountLike'] = LikeVideo::where('video_id', $getvideo->id)->count();
                }else{
                    $all['CountLike'] =0;
                }
                if(isset($request['idUser']))
                {
                    $user=$functionController->getUserInfor($request['idUser']);
                    if (!empty($user)) {
                        $all['status'] = LikeVideo::where('user_id', $user->id)->exists();
                    }
                }
                return $all;
            }
            else{
                return false; 
            }
            
        }
        public function countShare(Request $request)
        {
            $functionController = new functionController();
            if(isset($request['idVideo']))
            {
                    $getvideo=$functionController->getVideo($request['idVideo']);
                        if(!empty($getvideo))
                        {
                            $all=shareVideo::where('video_id', $getvideo->id)->count();
                            return $all;
                        }
                        return false;  
                    }
            else{
                return false; 
            }
            
        }
        public function addlike(Request $request)
        {
            $functionController = new functionController();
            if(isset($request['idVideo'])&& isset($request['idUser']))
                {
                    $likeRow=$functionController->checkLike($request['idVideo'],$request['idUser']);
                    if(empty($likeRow))
                    {
                        
                        $video = $functionController->getVideo($request['idVideo']);
                        $user = $functionController->getUserInfor($request['idUser']);
                        if(!empty($video))
                        {
                            $like = new LikeVideo;
                            $like->user_id= $user->id;
                            $like->video_id= $video->id;
                            $like->save();
                            return true;
                        }
                    }
                    else{
                        $likeRow->delete();
                        return true;
                    }
                    return false;
                }
            elseif(isset($request['idcmt'])&& isset($request['idUser']))    
            {
                $likeRow=$functionController->checkLikecmt($request['idcmt'],$request['idUser']);
                if(empty($likeRow))
                {
                    
                    $cmt = $functionController->getcmt($request['idcmt']);
                    $user = $functionController->getUserInfor($request['idUser']);
                    if(!empty($cmt))
                    {
                        $likecmt = new likecmt;
                        $likecmt->user_id= $user->id;
                        $likecmt->cmt_id= $cmt->id;
                        $likecmt->save();
                        return true;
                    }
                }
                else{
                    $likeRow->delete();
                    return true;
                }
                return false;
            }else{
                return false;
            }
        }
        public function addshare(Request $request)
        {
            $functionController = new functionController();
            if(isset($request['idVideo'])&& isset($request['idUser']))
            {
                    $video = $functionController->getVideo($request['idVideo']);
                    $user = $functionController->getUserInfor($request['idUser']);
                    if(!empty($video))
                    {
                        $share = new shareVideo;
                        $share->user_id= $user->id;
                        $share->video_id= $video->id;
                        $share->save();
                        return true;
                    }
                return false;
            }
        }
}
