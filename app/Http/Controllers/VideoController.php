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
use App\Models\album;
use App\Models\detailAlbum;
use App\Models\historyview;
use App\Models\savevideo;
use App\Models\cocreation;
use Carbon\Carbon;


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
                        $videoData = $video->toArray();
                        $videoData['id'] = $this->hashfuc($videoData['id']); 

                        $functionController = new functionController();
                        $linkVideo = $functionController->getImage($videoData['thumbNail']);
                        $videoData['thumbNail'] = $linkVideo;
                        $videoData['adressVideo'] = $functionController->getVideoSource($videoData['adressVideo']);
                        
                        $user = UserInformation::find($videoData['idUser']);
                        $videoData['idUser'] = $this->hashfuc($videoData['idUser']); 
                        $videoData['nameUser'] = $user ? $user->name : null;
                        $videoData['avatarUser']=empty($user['avatar']) ? null : $functionController->getImage($user['avatar']);
                        $a =DB::table('ratings')->where('user_id', $user->id)->avg('rating');
                        $videoData['userRating']= round($a, 1);
                      
                        return  $videoData;
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
                        $cocreationData["user_id"] = $this->hashfuc($cocreation->user_id);
                        $userid = $cocreation->user_id;
                        $user = userinformation::find($userid);
                        if ($user) {
                            $cocreationData["name"] = $user->name;
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
                    if(isset($request['idUser']))
                    {
                        if(!empty($request['idUser']))
                        {
                            $user=$functionController->getUserInfor($request['idUser']);
        
                            if (!empty($user)) {
                                $all['status'] = LikeVideo::where('user_id', $user->id)->where('video_id',$getvideo->id)->exists();
                            }
                        }
                            
                    }
                }else{
                    $all['CountLike'] =0;
                }
               
                return $all;
            }
            else{
                return false; 
            }
            
        }
        public function CheckLikeCmt(Request $request)
        {
            $functionController = new functionController();
            if(isset($request['idcmt']) ){
                $getcmt=$functionController->getcmt($request['idcmt']);
               
                $all=[];
                if (!empty($getcmt)) {
                    $all['CountLike'] = likecmt::where('cmt_id', $getcmt->id)->count();
                    if(isset($request['idUser']))
                    {
                        if(!empty($request['idUser']))
                        {
                            $user=$functionController->getUserInfor($request['idUser']);
        
                            if (!empty($user)) {
                                $all['status'] = likecmt::where('user_id', $user->id)->where('cmt_id',$getcmt->id)->exists();
                            }
                        }
                            
                    }
                }else{
                    $all['CountLike'] =0;
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
                        return $all;
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
                    if(!empty($request['idVideo'])&&!empty($request['idUser']))
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
                            
                            }
                        }
                        else{
                            $likeRow->delete();

                        }
                        return $this->CheckLikeVideo($request);
                     }
                     else{
                        return false;
                     }
                    
                }
            elseif(isset($request['idcmt'])&& isset($request['idUser']))    
            {
                if(!empty($request['idcmt'])&&!empty($request['idUser']))
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
                      
                    }
                }
                else{
                    $likeRow->delete();
                 
                }
                return $this->CheckLikeCmt($request);
                 }else{
                    return false;
                }
            }else{
                return false;
            }
        }
        public function addshare(Request $request)
        {
            $functionController = new functionController();
            if(isset($request['idVideo'])&& isset($request['idUser']))
            {
                if(!empty($request['idVideo'])&&!empty($request['idUser']))
                {
                    $video = $functionController->getVideo($request['idVideo']);
                    $user = $functionController->getUserInfor($request['idUser']);
                    if(!empty($video))
                    {
                        $share = new shareVideo;
                        $share->user_id= $user->id;
                        $share->video_id= $video->id;
                        $share->save();
                        return $this->countShare($request);
                    }
                }
                return false;
            }
        }
        public function showUserVideo($id)
        {
            if (!empty($id)) {
                $functionController = new functionController();
                $videos = Video::all();
                $videodata = [];
                foreach ($videos as $video) {
                    $hashedIdUser = hash('sha256', $video->idUser);
                    if (hash_equals($hashedIdUser, $id)) {
                        $videoArray = $video->toArray();
                        $videoArray['idUser'] = $functionController->hashfuc($video->idUser);
                        $videoArray['addressVideo'] = $functionController->getImage($video->adressVideo);
                        $idsave = $video['id'];
                        unset($videoArray['adressVideo'], $videoArray['updated_at'], $videoArray['id']);
                        $videoArray['id'] = $functionController->hashfuc($idsave);
                        $videoArray['thumbNail'] = $functionController->getImage($video->thumbNail);
                        $videodata[] = $videoArray;
                    }
                }
                return response()->json($videodata);
            }
            return null;
        }
        public function showAlbum($id){
            if(!empty($id)){
                $functionController = new functionController();
                $data= $functionController->getAllalbum($id);
                if(!empty($data))
                {
                    $dataR=[];
                    foreach ($data as $videoArray) {
                        $row =$videoArray->toArray();
                        $save = $row['id'];
                        $row['countVideo']=detailAlbum::where('album_id',$save)->count();
                        $row['thumbnail'] = empty($row['thumbnail']) ? null : $functionController->getImage($row['thumbnail']);
                        
                        unset($row['updated_at'], $row['id'],$row['user_id']);
                        $row['id'] = $functionController->hashfuc($save);
                        $dataR[] = $row;
                    }
                        return $dataR;
                 }
                 else{
                    return null;
                 }
            }else{
                return null;
            }
        }
        public function showvideoAlbum($id){
            if(!empty($id)){
                $functionController = new functionController();
                $getAlbum= $functionController->getAlbum($id);
                if(!empty($getAlbum))
                {
                    $data=detailAlbum::where('album_id',$getAlbum['id'])->get();
                    $dataR=[];
                    foreach($data as $row)
                    {
                        $videoArray = $row->toArray();
                        $save=$videoArray['id'];
                        $videoArray['video_id']=$functionController->hashfuc($row['video_id']);
                        $getVideo= $functionController->getVideo($videoArray['video_id']);
                        $videoArray['idUser'] = $functionController->hashfuc($getVideo->idUser);
                        $videoArray['addressVideo'] =empty($getVideo->adressVideo) ? null : $functionController->getImage($getVideo->adressVideo);
                        
                        $idsave = $getVideo['id'];
                        $videoArray['thumbNail'] =  empty($getVideo->thumbNail) ? null : $functionController->getImage($getVideo->thumbNail);
                   
                        $videoArray['album_id']=$functionController->hashfuc($row['album_id']);
                        $videoArray['titleVideo']=$getVideo['titleVideo'];
                        unset($videoArray['adressVideo'], $videoArray['updated_at'], $videoArray['id']);
                        $videoArray['id'] = $functionController->hashfuc($save);
                        $dataR[]=$videoArray;
                    }
                    return $dataR;
                }
            }else{
                return null;
            }
        }
        public function showHistory($id)
        {
            if (!empty($id)) {
                $functionController = new FunctionController();
                $getUser = $functionController->getUserInfor($id);
        
                if (!empty($getUser)) {
                    $getVideoUser = $functionController->getVideoHistory($getUser->id);
        
                    if (!empty($getVideoUser)) {
                        $data = [];
                        $dataYesterday = [];
                        $dataOld = [];
        
                        foreach ($getVideoUser as $video) {
                            $hashedId = hash('sha256', $video->id);
                            $getvideo = $functionController->getVideo($hashedId);
        
                            if (!empty($getvideo)) {
                                $getvideo1 = $getvideo->toArray();
                                $getvideo1['id'] = hash('sha256', $getvideo->id);
                                $getvideo1['thumbNail'] = $functionController->getImage($getvideo['thumbNail']);
                                $getvideo1['addressVideo'] = $functionController->getVideoSource($getvideo['adressVideo']);
                                $getvideo1['idUser'] = hash('sha256', $getvideo->idUser);
                                unset($getvideo1['adressVideo']);
        
                                $createdAt = Carbon::parse($video->created_at);
                                $getvideo1['time'] = $createdAt->diffInDays();
        
                                if ($getvideo1['time'] < 2) {
                                    $dataYesterday[] = $getvideo1;
                                } else {
                                    $dataOld[] = $getvideo1;
                                }
                            }
                        }
        
                        $data['yesterday'] = array_reverse($dataYesterday);
                        $data['older'] = array_reverse($dataOld);
        
                        return $data;
                    }
                }
            }
        
            return null;
        }
        public function showFriend($id)
        {
            if(!empty($id)){
                $functionController = new functionController();
                $getUser= $functionController->getUserInfor($id);
                if(!empty($getUser))
                {
                    $data=$functionController->getFollower($getUser->id);
                    $dataR=[];
                    if(!empty($data))
                    {
                        foreach($data as $row)
                        {
                            $hashedId = hash('sha256', $getUser->id);
                            $hashedIdCheck = hash('sha256',$row->user_follower_id);
                            $return = $functionController->CheckFollow( $hashedId,$hashedIdCheck);
                            if($return == 2)
                            {   
                                $id=$row['user_follower_id'];
                                unset($row['id']);
                                $row['user_follower_id']=$functionController->hashfuc($row['user_follower_id']);
                                $row['avatar']=empty($row['avatar']) ? null : $functionController->getImage( $row['avatar']);
                                $a=DB::table('ratings')->where('user_id', $id)->avg('rating');
                                $row['userRating']= round($a, 1);
                                $dataR[]=$row;
                            }
                        }
                        return $dataR;
                    }
                   return null;
                }
            }else{
                return null;
            }
        }
        public function addVideo(Request $request)
        {
    
            if(isset($request['titleVideo']) &&
            isset($request['descriptions']) &&
            isset($request['cocreations']) &&
            isset($request['idAlbum']) &&
            isset($request['idUser']) &&
            isset($request['hashtag'])&&
            isset($request['idCategory'])
            )
            {
               
                if(!empty($request['titleVideo']) &&
                !empty($request['descriptions']) &&
                !empty($request['idUser']) &&
                !empty($request['hashtag']))
                { 
                    $functionController = new functionController();
                    $idnewVideo=1;
                    $idU;
                    // if ($request->hasFile('video')) {
                        
                    //         $video = $request->file('video');
                    //         $videoName = time() . '.' . $video->getClientOriginalExtension();
                    //         $video->move(public_path('storage/video'), $videoName);
                        
                    //         $thumbnail = $request->file('thumbnail');
                    //         $thumbnailname = time() . '.' . $thumbnail->getClientOriginalExtension();
                    //         $thumbnail->move(public_path('storage/img'), $thumbnail);
                
                        $data=new Video();
                        $data['titleVideo']= $request->input('titleVideo');
                        $data['descriptions']=$request->input('descriptions');
                        //$data['adressVideo']=$videoName;
                        $data['view_count']= "0";
                        $idUser= $functionController->getUserInfor($request->idUser);
                        $data['idUser']=$idUser->id;
                        //$data['thumbNail']="thumbnail/".$thumbnailname;
                        $data->save();
                        $idnewVideo=$data['id'];
                        $idU=$idUser->id;
                    //}    
                    if(!empty($request['cocreations']))
                    {
                        $ids = explode(',', $request['cocreations']);
                        foreach ($ids as $id) {
                            $dataCo= new cocreation();
                            $getUser=$functionController->getUserInfor( $id);
                            if(!empty($getUser))
                            {
                                $dataCo['user_id']=$getUser['id'];
                                $dataCo['video_id']=$idnewVideo;
                            }
                            $dataCo->save();
                        }
                    }
                 
                    if(!empty($request['idAlbum']))
                    { 
                            $dataAL= new detailAlbum();
                            $album =$functionController->getAlbum($request['idAlbum']);
                            if(!empty($album))
                            {
                            $dataAL['album_id']= $album->id;
                            $dataAL['video_id']=$idnewVideo;
                            } 
                            $dataAL->save();
                    }
                    return true;
                }
            
            }
            return false;
        }
        public function addHistory(Request $request){
            if(isset($request['idUser'])&& isset($request['idVideo']))
            {
                if(!empty($request['idUser'])&& !empty($request['idVideo']))
                {
                    $functionController = new functionController();
                    $video=$functionController->getVideo($request['idVideo']);
                    $user=$functionController->getUserInfor($request['idUser']);
                    if(!empty( $video)&& !empty( $user))
                    {
                        if($functionController->checkHistory($video->id,$user->id)===true)
                        {
                            $data=new historyview();
                            $data['user_id']=$user['id'];
                            $data['video_id']=$video['id'];
                            $data->save();
                            return "ok"; 
                        }
                      
                    }
                   
                }
                return null;
            }
        }
        public function addSave(Request $request){
            if(isset($request['idUser'])&& isset($request['idVideo']))
            {
                if(!empty($request['idUser'])&& !empty($request['idVideo']))
                {
                    $functionController = new functionController();
                    $video=$functionController->getVideo($request['idVideo']);
                    $user=$functionController->getUserInfor($request['idUser']);
                    if(!empty( $video)&& !empty( $user))
                    {
                        if($functionController->checkHistory($video->id,$user->id)===true)
                        {
                            $data=new savevideo();
                            $data['user_id']=$user['id'];
                            $data['video_id']=$video['id'];
                            $data->save();
                            return "ok";
                        }
                      
                    }
                   
                }
                return null;
            }
        }
        public function wasLike($id)
        {
            if (isset($id) && !empty($id)) {
                $functionController = new FunctionController();
                $likevideos = LikeVideo::all();
                $data = [];
        
                foreach ($likevideos as $likevideo) {
                    $hashedIdUser = hash('sha256', $likevideo->user_id);
        
                    if (hash_equals($hashedIdUser, $id)) {
                        $hashedIdVideo = hash('sha256', $likevideo->video_id);
                        $getvideo = $functionController->getVideo($hashedIdVideo);
        
                        if (!empty($getvideo)) {
                            $getvideo1 = $getvideo->toArray();
                            $getvideo1['id'] = hash('sha256', $getvideo->id);
                            $getvideo1['thumbNail'] = $functionController->getImage($getvideo->thumbNail);
                            $getvideo1['addressVideo'] = $functionController->getVideoSource($getvideo->adressVideo);
                            $getvideo1['idUser'] = hash('sha256', $getvideo->idUser);
                            unset($getvideo1['adressVideo'],$getvideo1['idUser']);
                            $data[] = $getvideo1;
                        }
                    }
                }
                return $data;
            }
            return null;
        }
        public function wasSave($id)
        {
            if (isset($id) && !empty($id)) {
                $functionController = new FunctionController();
                $likevideos = savevideo::all();
                $data = [];
        
                foreach ($likevideos as $likevideo) {
                    $hashedIdUser = hash('sha256', $likevideo->user_id);
        
                    if (hash_equals($hashedIdUser, $id)) {
                        $hashedIdVideo = hash('sha256', $likevideo->video_id);
                        $getvideo = $functionController->getVideo($hashedIdVideo);
        
                        if (!empty($getvideo)) {
                            $getvideo1 = $getvideo->toArray();
                            $getvideo1['id'] = hash('sha256', $getvideo->id);
                            $getvideo1['thumbNail'] = $functionController->getImage($getvideo->thumbNail);
                            $getvideo1['addressVideo'] = $functionController->getVideoSource($getvideo->adressVideo);
                            $getvideo1['idUser'] = hash('sha256', $getvideo->idUser);
                            unset($getvideo1['adressVideo'],$getvideo1['idUser']);
                            $data[] = $getvideo1;
                        }
                    }
                }
                return array_reverse($data);
            }
            return null;
        }
}
