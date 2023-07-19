<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\follow;
use App\Models\userinformation;
use Illuminate\Support\Facades\DB;

class followController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return follow::all();
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function show(Request $request)
    {
        if (isset($request['idUser'])) {
            if (!empty($request['idUser'])) {
                $data = follow::all();
                foreach ($data as $row) {
                    $hashedId = hash('sha256', $row->user_id);
                    if (hash_equals($hashedId, $request['idUser'])) {
                        $functionController = new functionController();
                        $data1 = follow::where('user_id', $row->user_id)
                            ->join('userinformations', 'follows.user_follower_id', '=', 'userinformations.id')
                            ->select( 'follows.user_follower_id', 'userinformations.name', 'userinformations.avatar')
                            ->get();
                            $returndata=[];
            
                            foreach ($data1 as $row) {
                                $Arrcmt = $row->toArray();
                                $Arrcmt['user_follower_id']=$functionController->hashfuc($row['user_follower_id']);
                                $Arrcmt['name']=$row['name'];
                                $Arrcmt['avatar']=$functionController->getImage($row['avatar']);
                                $averageRating = DB::table('ratings')->where('user_id', $row->user_follower_id)->avg('rating');
                                $Arrcmt["userRating"] = round($averageRating, 1);
                                $check=$functionController->CheckFollow( $request['idUser'],$Arrcmt['user_follower_id']);
                                if($check == 2)
                                {
                                   $a="RS002";
                                }elseif($check==1)
                                {
                                    $a= "RS001";
                                }elseif($check==0)
                                {
                                    $a= "RS000";
                                }else{
                                    $a="RS003";
                                }
                                $Arrcmt['status']=$a;
                                $returndata[]=$Arrcmt;
                            }
                        return $returndata;
                    }
                }
            }
        }
        return false;
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
