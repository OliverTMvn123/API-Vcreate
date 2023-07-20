<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\about_us;
use App\Models\category;
use App\Models\contenthp;
use App\Models\imagehp;
use App\Models\logo;
use App\Models\slogan;
use App\Models\startwithus;
use App\Models\titlehomepage;
use App\Models\videohomepage;
use App\Services\functionServices;

class HomepageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 
        
        return ;
        
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
    public function show($id)
    {
        $all=array();
        switch($id)
        {
            case 'aboutus':
                {
                   $all= about_us::all();
                   break;
                }
            case 'category1':
                {
                    $all= category::all();
                    break;
                }
            case 'contenthp':
                {
                    $all= contenthp::all();
                    break;
                }
            case 'imagehp':
                {
                    $images= imagehp::all();
                    $all = [];
                        
                        foreach ($images as $image) {
                            $link = $image['address'];
                            $newLink = $this->getImage($link);
                            $image['address'] = $newLink;
                            $all[] = $image;
                        }
                    break;
                }   
            case 'logo':
                {
                    $all= logo::all();
                    break;
                }   
            case 'slogan':
                {
                    $all= slogan::all();
                    break;
                }
            case 'startwithus':
                {
                    $all= startwithus::all();
                    break;
                } 
            case 'titlehomepage':
                {
                    $all= titlehomepage::all();
                    break;
                }  
            case 'video':
                    {
                        $videos = videohomepage::all();
                        $all = [];
                        
                        foreach ($videos as $video) {
                            $link = $video['address'];
                            $newLink = $this->getVideo($link);
                            $video['address'] = $newLink;
                            $all[] = $video;
                        }
                        break;
                    }  
            default:
                $all='error';
                    
        }
        return response()->json($all);
    }
    
    public function show1($name, $id)
    {
        $all = array();
        switch ($name) {
            case 'aboutus': {
                $all = about_us::find($id);
                break;
            }
            case 'category': {
                $all = category::find($id);
                break;
            }
            case 'contenhp': {
                $all = contenhp::find($id);
                break;
            }
            case 'imagehp': {
                $all = imagehp::find($id);
                $link = $all['address'];
                $getpic = $this->getImage($link);
                $all['address'] = $getpic;
                break;
            }   
            case 'logo': {
                $all = logo::find($id);
                break;
            }   
            case 'slogan': {
                $all = slogan::find($id);
                break;
            }
            case 'startwithus': {
                $all = startwithus::find($id);
                break;
            } 
            case 'titlehomepage': {
                $all = titlehomepage::find($id);
                break;
            }  
            case 'video': {
                $all = videohomepage::find($id);
                $link = $all['address'];
                $getpic = $this->getVideo($link);
                $all['address'] = $getpic;
                break;
            }  
            default:
                $all = 'error';
        }
        
        return response()->json($all);
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
