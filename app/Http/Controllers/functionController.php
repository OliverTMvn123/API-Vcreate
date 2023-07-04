<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;
use App\Models\userinformation;
use App\Models\category;
use App\Models\comments;
use App\Models\likecmt;
use App\Models\LikeVideo;

class functionController extends Controller
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
}
