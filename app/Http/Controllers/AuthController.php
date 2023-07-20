<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\TemporaryRequest;
use App\Mail\OTPVerification;
use App\Models\User;
use App\Models\Role;
use App\Models\notification;
use App\Models\linksocal;
use App\Models\userinformation;
use App\Models\rating;
use App\Services\functionServices;
use Carbon\Carbon;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
       
        if (Auth::attempt($credentials)) {
           
            $user = Auth::user();
            $user->load('role');
            $tam = $user->load('information');
            $token = $user->createToken('authToken')->plainTextToken;
            $functionServices = new functionServices();
            $data = [
                'id' => hash('sha256', $user->information['id']),
                'email' => $user['email'],
                'role' => hash('sha256', $user['role']->id),
                'role_name' => $user['role']->name,
                'infor' => $user->information->name,
                'avatar' => empty($user->information->avatar) ? null : $functionServices->getImage($user->information->avatar),
                'access_token' => $token,
            ];
            return  response()->json($data);
        }
        $text = 'false';
        return response()->make($text);
        //return $request;
    }
    public function checkEmailex(Request $request)
    {
        return User::where('email', $request->email)->exists();

    }
    public function checkSaveRequest(Request $request,$type)
    {
        return TemporaryRequest::where('email', $request->email)->where('type', $type)->exists();

    }
    public function Mail(Request $request)
    {
        if ($this->checkEmailex($request) === false) {
            if (!$this->checkSaveRequest($request, 1)) {
            $saveRe = new TemporaryRequest();
            $saveRe->name = $request->input('name');
            $saveRe->email = $request->input('email');
            $saveRe->password = $request->input('password');
            $saveRe->type=1;
            $otp = Str::random(4);
            $saveRe->OTP = $otp;
            $saveRe->save();
            }
            else{
                $saveRe = TemporaryRequest::where('email', $request->email)->where('type', 1)->first();
                if ($saveRe) {
                    $otp = Str::random(4);
                    $saveRe->OTP = $otp;
                    $saveRe->save();
                }
            }
            $otpMessage = "Xin chào " . $request->input('name') . "," . PHP_EOL . PHP_EOL;
            $otpMessage .= "Mã OTP của bạn là: $otp" . PHP_EOL . PHP_EOL;
            $otpMessage .= "Xin vui lòng sử dụng mã OTP này để xác thực tài khoản của bạn." . PHP_EOL . PHP_EOL;
            $otpMessage .= "Trân trọng," . PHP_EOL;
            $otpMessage .= "Đội ngũ hỗ trợ";

            Mail::raw($otpMessage, function (Message $message) use ($request) {
                $message->to($request->input('email'));
                $message->subject('Xác thực OTP');
            });
            $VideoController = new VideoController();
            $data = [
                'idRequest' => $VideoController->hashfuc($saveRe->id),
            ];
            return response()->json($data);
        } else {
            return false;
        }
    }
    public function changeOTP($otp)
    {
        return $otp[0].$otp[1].$otp[2].$otp[3];
    }
    public function register(Request $request)
    {
        if (isset($request['idRequest'])) {
    $savedRequest = TemporaryRequest::all();
    $idsave = null; // Khởi tạo giá trị ban đầu của $idsave

    foreach ($savedRequest as $save) {
        $hashedId = hash('sha256', $save->id);
        
        if (hash_equals($hashedId, $request['idRequest'])) {
            $idsave = $save->id;
            $OTPChange = $this->changeOTP($request['OTP']);

            if ($OTPChange == $save->OTP) {
                $user = new User();
                $user->name = $save->name;
                $user->email = $save->email;
                $user->password = Hash::make($save->password);
                $user->role_id = Role::where('name', 'creator')->value('id');

                $user->save();

                $user->information()->create([
                    'name' => $save->name,
                    'address' => 'Địa chỉ',
                    'phoneNumber' => '099999999',
                ]);

                TemporaryRequest::find($idsave)->delete();
                return true;
            }
        }
    }
} 

        return false;
    }
    //forgot pasword 
    public function Mailforgot(Request $request)
    {
        if ($this->checkEmailex($request) === true) {
            $user = User::where('email', $request->email)->first();
            if (!$this->checkSaveRequest($request, 0)) {
                
                  
                    $saveRe = new TemporaryRequest();
                    $saveRe->name = NULL;
                    $saveRe->email = $request['email'];
                    $saveRe->password = NULL;
                    $saveRe->type=0;
                    $otp = Str::random(4);
                    $saveRe->OTP = $otp;
            }
            else{
                $saveRe = TemporaryRequest::where('email', $request->email)->where('type', 0)->first();
                if ($saveRe) {
                    $otp = Str::random(4);
                    $saveRe->OTP = $otp;
                    $saveRe->save();
                }
            }
            $saveRe->save();
            $otpMessage = "Xin chào " . $request->input('name') . "," . PHP_EOL . PHP_EOL;
            $otpMessage .= "Mã OTP của bạn là: $otp" . PHP_EOL . PHP_EOL;
            $otpMessage .= "Xin vui lòng sử dụng mã OTP này để xác thực tài khoản của bạn." . PHP_EOL . PHP_EOL;
            $otpMessage .= "Trân trọng," . PHP_EOL;
            $otpMessage .= "Đội ngũ hỗ trợ";

            Mail::raw($otpMessage, function (Message $message) use ($request) {
                $message->to($request->input('email'));
                $message->subject('Xác thực OTP');
            });
            $VideoController = new VideoController();
            $data = [
                'idUser' => $VideoController->hashfuc($user->id),
                'idRequest' => $VideoController->hashfuc($saveRe->id),
            ];
            return response()->json($data);
        } else {
            return false;
        }
    }

 
    public function verifyForgotEmail(Request $request)
    {
        if (isset($request['idRequest']) && isset($request['idUser']) && isset($request['OTP'])) {
            $savedRequest = TemporaryRequest::all();
            $user = $this->getUser($request['idUser']);
            if (!empty($user)) {
                foreach ($savedRequest as $save) {
                    $hashedIdR = hash('sha256', $save->id);
                    if (hash_equals($hashedIdR, $request['idRequest'])) {
                        if ($save->OTP == $request['OTP']) {
                            $save->delete();
                            $VideoController = new VideoController();
                            $data = [
                                'idUser' => $VideoController->hashfuc($user->id),
                            ];
                            return response()->json($data);
                        } else {
                            return false;
                        }
                    }
                }
                return false;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function forgotPass(Request $request)
    {
        if (isset($request['idUser']) && isset($request['password'])) {
            $user = $this->getUser($request['idUser']);
            if (!empty($user)) {
                $user->password = Hash::make($request['password']);
                $user->save();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function resendOTP(Request $request)
    {
        if (isset($request['idRequest'])) {
            $savedRequest = TemporaryRequest::all();
            foreach ($savedRequest as $save) {
                $hashedIdR = hash('sha256', $save->id);
                if (hash_equals($hashedIdR, $request['idRequest'])) {
                    $otp = Str::random(4);
                    $save->OTP = $otp;

                    $save->save();
                    $otpMessage = "Xin chào ," . PHP_EOL . PHP_EOL;
                    $otpMessage .= "Mã OTP của bạn là: $otp" . PHP_EOL . PHP_EOL;
                    $otpMessage .= "Xin vui lòng sử dụng mã OTP này để xác thực tài khoản của bạn." . PHP_EOL . PHP_EOL;
                    $otpMessage .= "Trân trọng," . PHP_EOL;
                    $otpMessage .= "Đội ngũ hỗ trợ";

                    Mail::raw($otpMessage, function (Message $message) use ($save) {
                        $message->to($save['email']);
                        $message->subject('Xác thực OTP');
                    });
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }
    public function showUser(Request $request)
    {
       
        if(isset($request['idUser']))
        {
            $functionServices = new functionServices();
            $user = $functionServices->getUserInfor($request['idUser']);
            if(!empty($user))
            {
                $point= rating::where('user_id',$user->id)->avg('rating');
                $rating = round($point, 1);

                $follower= $functionServices->getFollower($user->id);
                $following= $functionServices->getFollowing($user->id);
                $videos= $functionServices->getVideoUser($user->id);

                $userData = [
                    'id' => hash('sha256', $user->id),
                    'name'=>$user->name,
                    'avatar' =>    empty($user->avatar) ? null : $functionServices->getImage($user->avatar),
                  
                    'background' =>  empty($user->background) ? null : $functionServices->getImage($user->background),
                    'follower'=> count($follower),
                    'following'=> count($following),
                    'countVideo'=> count($videos),
                    'rating'=> $rating ,
                    
                ];
                
                return response()->json($userData);
            }
        }
    }

    public function checkFollow(Request $request)
    {
        if(isset($request['idUser'])&&isset($request['idUserCheck']))
        {
            if(!empty($request['idUser']) && !empty($request['idUserCheck']))
            {
                if(hash_equals($request['idUser'],$request['idUserCheck']))
                {
                    return "RS004";
                }else{
                $functionServices = new functionServices();
                $check = $functionServices->checkFollow($request['idUser'],$request['idUserCheck']);
                if($check == 2)
                {
                    return "RS002";
                }elseif($check==1)
                {
                    return "RS001";
                }elseif($check==0)
                {
                    return "RS000";
                }else{
                    return "RS003";
                }
            }
            }
            return null;
        }
        return null;
    }
    public function showRating($id)
    {
        if(!empty($id))
        {
            $data=rating::all();
                foreach ($data as $row) {
                    $hashedId = hash('sha256', $row->user_id);
                    if (hash_equals($hashedId, $id)) {
                        $averageRating = DB::table('ratings')->where('user_id', $row->user_id)->avg('rating');
                        $rating["userRating"] = round($averageRating, 1);
                        return $rating;
                    }
                }
        }
        return null;
    }
    public function showDescription($id) 
    {
        if (!empty($id)) {
            $functionServices = new functionServices();
            $data = $functionServices->getUserInfor($id);
            $array = [];
    
            if (!empty($data)) {
                $linksocal = linksocal::where('user_id', $data['id'])->first();
                $array['descriptions'] = isset($data['descriptions']) ? $data['descriptions'] : null;
    
                if (!empty($linksocal)) {
                    $array['facebook'] = isset($linksocal->facebook) ? $linksocal->facebook : null;
                    $array['instagram'] = isset($linksocal->instagram) ? $linksocal->instagram : null;
                    $array['youtube'] = isset($linksocal->youtube) ? $linksocal->youtube : null;
                } else {
                    $array['facebook'] = null;
                    $array['instagram'] = null;
                    $array['youtube'] = null;
                }
                $array['createTime']=$data->created_at;
                
            } else {
                $array['descriptions'] = null;
            }
    
            $array[] = isset($data['descriptions']) ? $data['descriptions'] : null;
            unset($array['0']);
            return $array;
        }
    
        return null;
    }
    public function showNotification($id)
    {
        if (!empty($id)) {
            $functionServices = new functionServices();
            $getUser = $functionServices->getUserInfor($id);
            if (!empty($getUser)) {
                $data = notification::where("user_id", $getUser->id)->get();
             
                $reData = [];
                if (!empty($data)) {
                    $arrayToday = [];
                    $arrayOlder = [];
    
                    $nameCount = [];
    
                    foreach ($data as $noti) {
                        $hashid = $functionServices->hashfuc($noti['actionUser']);
                        $user = $functionServices->getUserInfor($hashid);
                        $video = $functionServices->get1Video($noti['video_id']);
                        if (!empty($user) ) {
                            $array = [];
                            $array['name'] = $user->name;
                            $array['avatar'] = $functionServices->getImage($user->avatar);
                            $array['type'] = $noti->type;
                            $array['video_id']=$noti->video_id;
                            if (($noti->type == 2 || $noti->type == 3) && !empty($video)) {
                            
                                $array['video']["idVideo"]= $functionServices->hashfuc($video->id);
                                   
                                $array['video']["thumbNail"]=$functionServices->getImage($video->thumbNail);
                                
                            }
                            else{
                                
                                $array['video']["idVideo"]= null;
                                   
                                $array['video']["thumbNail"]=null;
                            }
                            if ($noti->created_at) {
                                $createdAt = Carbon::parse($noti->created_at);
                                $array['time'] = $createdAt->diffInDays();
                                $array['created_at']=$noti->created_id;
                                if(!empty($array['video_id']))
                                {
                                    $key = $array['type'] . '_' . $array['time']. '_' . $array['video_id'];
                                }
                                else{
                                    $key = $array['type'] . '_' . $array['time'];
                                }
                
                                if (!isset($nameCount[$key])) {
                                    $nameCount[$key] = [];
                                }
                                $nameCount[$key][] = $array['name'];
                                $avatar[$key][]=$array['avatar'];
                                if ($array['time'] < 1) {
                                    $arrayToday[] = $array;
                                } else {
                                    $arrayOlder[] = $array;
                                }
                            }
                        }
                    }
                 
                    $mergedArrayToday = [];
                    foreach ($arrayToday as $item) {
                        if(!empty($item['video_id']))
                                {
                                    $key = $item['type'] . '_' . $item['time']. '_' . $item['video_id'];
                                }
                                else{
                                    $key = $item['type'] . '_' . $item['time'];
                                }
                        $nameList = $nameCount[$key];
                        $avatarList = $avatar[$key];
                   
                        if (count($nameList) < 2) {
                            $item['name'] = [
                                'names' => $nameList[0],
                            ];
                            $item['avatar']=[$avatarList[0]];
                            $item['name']['count'] = 0;
                        }
                        else{
                            $item['name'] = [
                                'names' => [$nameList[0],$nameList[1]]
                            ];
                            $item['avatar']= [$avatarList[0],$avatarList[1]];
                            $item['name']['count'] = count($nameList) - 2;
                        }
                        $mergedArrayToday[] = $item;
                    }
    
                    $mergedArrayOlder = [];
                    foreach ($arrayOlder as $item) {
                        $key = $item['type'] . '_' . $item['time']. '_' .$noti->video_id;
                        $nameList = $nameCount[$key];
                        $avatarList = $avatar[$key];
                   
                        if (count($nameList) < 2) {
                            $item['name'] = [
                                'names' => $nameList[0],
                            ];
                            $item['avatar']=[$avatarList[0]];
                            $item['name']['count'] = 0;
                        }
                        else{
                            $item['name'] = [
                                'names' => [$nameList[0],$nameList[1]]
                            ];
                            $item['avatar']= [$avatarList[0],$avatarList[1]];
                            $item['name']['count'] = count($nameList) - 2;
                        }
                        $mergedArrayOlder[] = $item;
                    }
                    
                    $endDataToday=[];
                    $i=0;
                    $tam;
                    $endDataOlder=[];
                    $i1=0;
                    $tam1;
                    foreach ($mergedArrayToday as $item) 
                        {
                            if($i==0)
                            {
                                $tam=$item['name'];
                                unset($item['time']);
                                $endDataToday[]=$item;
                            }
                        
                            if($i!=0)
                            {
                               if($item['name']!==$tam)
                               {
                                unset($item['time']);
                                    $endDataToday[]=$item;
                                    $tam=$item['name'];
                               }
    
                            }
                            $i++;
                        }
                        foreach ($mergedArrayOlder as $item) 
                        {
                            if($i1==0)
                            {
                                $tam1=$item['name'];
                                unset($item['time']);
                                $endDataOlder[]=$item;
                               
                            }
                        
                            if($i1!=0)
                            {
                               if($item['name']!==$tam1)
                               {
                                unset($item['time']);
                                    $endDataOlder[]=$item;
                                    
                                    $tam1=$item['name'];
                               }
    
                            }
                            $i1++;
                        }
                        
                    $reData['new'] = collect($endDataToday)->sortBy('time')->values()->all();
                    $reData['older'] = collect($endDataOlder)->sortBy('time')->values()->all();
                }
                return $reData;
            }
        }
        return null;
    }
    
    
    
    
    
}