<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\userinformation;
use App\Models\category;

use App\Http\Controllers\HomepageController;
use App\Http\Controllers\VideoController;
class flatformController extends Controller
{
public function trending(){
        $data = Video::all();
        $all = [];
        foreach ($data as $video) {
            $likesCount = $video->likevideos()->count();
            $cocreation = $video->cocreation()->get();
            $user = UserInformation::find($video['idUser']);
            $functionController = new functionController();
            $VideoController = new VideoController();
            $id1 = $VideoController->hashfuc($video['id']); 
            $videoArray = $video->toArray();
            $videoArray['adressVideo']= $functionController->getVideoSource($videoArray['adressVideo']);
            $videoArray['thumbNail'] = $functionController->getImage($videoArray['thumbNail']);
            $videoArray['avatar'] = empty($user['avatar']) ? null : $functionController->getImage($user['avatar']);
            $user = UserInformation::find($videoArray['idUser']);
            $videoArray['nameUser'] = $user ? $user->name : null;
            $videoArray['idUser'] = $VideoController->hashfuc($videoArray['idUser']);
        
            $videoArray['likeCount'] = $likesCount;
            $videoArray['shareCount'] = 0;
            
            unset($videoArray['id']); 
            $newVideo = array_merge(['id' => $id1], $videoArray);
            
            $all[] = $newVideo;
            if(count($all)==10)
            {
                break;
            }
        }
        
        return response()->json($all);
    }
    public function foryou(){
        $data = Video::all();
        $all = [];
        foreach ($data as $video) {
            $likesCount = $video->likevideos()->count();
            $cocreation = $video->cocreation()->get();
            $user = UserInformation::find($video['idUser']);
            $functionController = new functionController();
            $VideoController = new VideoController();
            $id1 = $VideoController->hashfuc($video['id']); 
            $videoArray = $video->toArray();
            $videoArray['adressVideo']= $functionController->getVideoSource($videoArray['adressVideo']);
            $videoArray['thumbNail'] = $functionController->getImage($videoArray['thumbNail']);
            $videoArray['avatar'] = empty($user['avatar']) ? null : $functionController->getImage($user['avatar']);
            $user = UserInformation::find($videoArray['idUser']);
            $videoArray['nameUser'] = $user ? $user->name : null;
            $videoArray['idUser'] = $VideoController->hashfuc($videoArray['idUser']);
            $videoArray['likeCount'] = $likesCount;
            $videoArray['shareCount'] = 0;

            unset($videoArray['id']); 
            $newVideo = array_merge(['id' => $id1], $videoArray);
            
            $all[] = $newVideo;
            if(count($all)==20)
            {
                break;
            }
        }
        
        return response()->json($all);
    }
    public function categories()
    {
        $functionController= new functionController();
        $data = Category::all();
        $all = [];
        foreach ($data as $category) {
            $VideoController = new VideoController();
            $id1 = $VideoController->hashfuc($category['id']);
            $category1 = $category->toArray();
            unset($category1['id']);
            unset($category1['created_at']);
            unset($category1['updated_at']); 
            $category1['background']=    empty($category['background']) ? null : $functionController->getImage($category['background']);
        
            $newCategory = array_merge(['id' => $id1], $category1);
            $all[] = $newCategory;
           
        }
        return response()->json($all);
    }
}