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
use App\Http\Controllers\functionController;
use App\Mail\OTPVerification;
use App\Models\User;
use App\Models\Role;
use App\Models\linksocal;
use App\Models\userinformation;
use App\Models\rating;

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
            $functionController = new functionController();
            $data = [
                'id' => hash('sha256', $user->information['id']),
                'email' => $user['email'],
                'role' => hash('sha256', $user['role']->id),
                'role_name' => $user['role']->name,
                'infor' => $user->information->name,
                'avatar' => empty($user->information->avatar) ? null : $functionController->getImage($user->information->avatar),
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
    // public function showAlluser()
    // {

    //     $users = User::all();

    //     $data = [];
    //     $homepageController =new HomepageController();
    //     foreach ($users as $user) {
    //         $user->load('role');
    //         $user->load('information');
    //         if (empty($user['avatar'])) {
    //             $user->information['avatar'] = null;
    //         } else {
                
    //             $user->information['avatar'] = $homepageController->getImage($user['avatar']);
    //         }
    //         $userData = [
    //             'id' => hash('sha256', $user->id),
    //             'email' => $user->email,
    //             'phoneNumber' => $user->information->phoneNumber,
    //             'address' => $user->information->address,
    //             'descriptions' => $user->information->descriptions,
    //             'avatar' => $user->information->avatar,
    //             'gender' => $user->information->gender,
    //             'role' => hash('sha256', $user->role->id),
    //             'role_name' => $user->role->name,
    //             'infor' => $user->information->name,
    //         ];

    //         $data[] = $userData;
    //     }

    //     return $data;
    // }
    public function showUser(Request $request)
    {
       
        if(isset($request['idUser']))
        {
            $functionController = new functionController();
            $user = $functionController->getUserInfor($request['idUser']);
            if(!empty($user))
            {
                $point= rating::where('user_id',$user->id)->avg('rating');
                $rating = round($point, 1);

                $follower= $functionController->getFollower($user->id);
                $following= $functionController->getFollowing($user->id);
                $videos= $functionController->getVideoUser($user->id);

                $userData = [
                    'id' => hash('sha256', $user->id),
                    'name'=>$user->name,
                    'avatar' =>    empty($user->avatar) ? null : $functionController->getImage($user->avatar),
                  
                    'background' =>  empty($user->background) ? null : $functionController->getImage($user->background),
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
                $functionController = new functionController();
                $check = $functionController->checkFollow($request['idUser'],$request['idUserCheck']);
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
            $functionController = new functionController();
            $data = $functionController->getUserInfor($id);
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
}