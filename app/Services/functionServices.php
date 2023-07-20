<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;
use App\Models\userinformation;
use App\Models\category;
use App\Models\comments;
use App\Models\likecmt;
use App\Models\LikeVideo;
use App\Models\follow;
use App\Models\album;
use App\Models\deltailAlbum;
use App\Models\historyview;


use App\Models\user;
class functionServices
{
    public function getVideo($id)
    {
        $videos = Video::all();
        foreach ($videos as $video) {
            $hashedId = hash('sha256', $video->id);
            if (hash_equals($hashedId, $id)) {
                return $video;
            }
        }
        return null; // Trả về null nếu không tìm thấy video
    }
 
    public function getVideoUser($id)
    {
        $videos = Video::all();
        $data=[];
        foreach ($videos as $video) {
            if ($video->idUser==$id){
                $data[]= $video;
            }
        }
        return $data;
    }
    public function get1Video($id)
    {
        $videos = Video::all();
        $data=[];
        foreach ($videos as $video) {
            if ($video->idUser==$id){
                return $video;
            }
        }
        return null;
    }
    public function getVideoHistory($id)
    {
        $videos = historyview::all();
        $data=[];
        foreach ($videos as $video) {
            if ($video->user_id==$id){
                $data[]= $video;
            }
        }
        return $data;
    }
    public function checkHistory($idVideo,$idUser)
    {
        $videos = historyview::all();
        foreach ($videos as $video) {
            if ($video->user_id==$idUser && $video->video_id == $idVideo )
            {
                $video->delete();
            }
        }
        return true;
    }
    public function getcmt($id)
    {
        $cmts = comments::all();
        foreach ($cmts as $cmt) {
            $hashedId = hash('sha256', $cmt->id);
            if (hash_equals($hashedId, $id)) {
                return $cmt;
            }
        }
        return null; // Trả về null nếu không tìm thấy video
    }
    public function getcategory($id)
    {
        $getcategory = category::all();
        foreach ($getcategory as $cmt) {
            $hashedId = hash('sha256', $cmt->id);
            if (hash_equals($hashedId, $id)) {
                return $cmt;
            }
        }
        return null; // Trả về null nếu không tìm thấy video
    }
    public function checkLike($idVideo,$idUser)
    {
        $likevideos = LikeVideo::all();
        foreach ($likevideos as $likevideo) {
            $hashedIdUser = hash('sha256', $likevideo->user_id);
            $hashedIdVideo = hash('sha256', $likevideo->video_id);
            if (hash_equals($hashedIdVideo, $idVideo) && hash_equals($hashedIdUser, $idUser) ) {
                return $likevideo;
            }
        }
        return null; 
    }
    public function checkshare($idVideo,$idUser)
    {
        $likevideos = LikeVideo::all();
        foreach ($likevideos as $likevideo) {
            $hashedIdUser = hash('sha256', $likevideo->user_id);
            $hashedIdVideo = hash('sha256', $likevideo->video_id);
            if (hash_equals($hashedIdVideo, $idVideo) && hash_equals($hashedIdUser, $idUser) ) {
                return $likevideo;
            }
        }
        return null; 
    }
    public function checkLikecmt($idcmt,$idUser)
    {
        $likecmts = likecmt::all();
        foreach ($likecmts as $likecmt) {
            $hashedIdUser = hash('sha256', $likecmt->user_id);
            $hashedIdcmt = hash('sha256', $likecmt->cmt_id);
            if (hash_equals($hashedIdcmt, $idcmt) && hash_equals($hashedIdUser, $idUser) ) {
                return $likecmt;
            }
        }
        return null; 
    }
    public function getUser($id)
    {
        $users = User::all();
        foreach ($users as $user) {
            $hashedId = hash('sha256', $user->id);
            if (hash_equals($hashedId, $id)) {
                return $user;
            }
        }
        return null;
    }
    public function getUserInfor($id)
    {
        $users = userinformation::all();
        foreach ($users as $user) {
            $hashedId = hash('sha256', $user->id);
            if (hash_equals($hashedId, $id)) {
                return $user;
            }
        }
        return null;
    }
    public function getAllalbum($id)
    {
        $albums = album::all();
        $data=[];
        foreach ($albums as $album) {
            $hashedId = hash('sha256', $album->user_id);
            if (hash_equals($hashedId, $id)) {
                $data[]=$album;
            }
        }
        return $data;
    }
    public function getAlbum($id)
    {
        $albums = album::all();
        foreach ($albums as $album) {
            $hashedId = hash('sha256', $album->id);
            if (hash_equals($hashedId, $id)) {
                return $album;
            }
        }
        return null;
    }
    public function getImage($link)
    {
        $imagePath = 'img/' . $link;
        $imageUrl = Storage::url($imagePath);
        return url($imageUrl);
    }
    public function getVideoSource($link)
    {
        $VideoPath = 'video/' . $link;
        $VideoURL = Storage::url($VideoPath);
        return  url($VideoURL);;
    }
    public function hashfuc($item)
    {
        $tam=(string)$item;
        $re = hash('sha256', $item);
        return $re;
    }
    public function CheckFollow($idUser,$idUserCheck){
        
        $data = follow::all();
        $total=0;
        $total1=0;
        foreach ($data as $row) {
            $hashedId = hash('sha256', $row->user_id);
            $hashedIdCheck = hash('sha256', $row->user_Follower_id);
            if (hash_equals($hashedId, $idUser) && hash_equals($hashedIdCheck, $idUserCheck)) {
                $total++;
            }
            if (hash_equals($hashedId, $idUserCheck) && hash_equals($hashedIdCheck, $idUser)) {
                $total1++;
            }
        }
        if($total==1 && $total1==1)
        {
            return 2;
        }
        elseif($total==1 && $total1==0)
        {
            return 1;
        }
        elseif($total==0 && $total1==0)
        {
            return 0;
        }else{
            return -1;
        }
    }
    public function getFollower($idUser){
        $data = follow::where('user_id', $idUser)
        ->join('userinformations', 'follows.user_follower_id', '=', 'userinformations.id')
        ->select('follows.id','follows.user_follower_id','userinformations.name','userinformations.avatar')
        ->get();
        return $data;
    }
    public function getFollowing($idUser){
        $data = follow::where('user_Follower_id', $idUser)
        ->join('userinformations', 'follows.user_id', '=', 'userinformations.id')
        ->select('follows.id','follows.user_id','userinformations.name','userinformations.avatar')
        ->get();
        return $data;
    }
}
?>