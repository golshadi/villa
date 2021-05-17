<?php

namespace app;
// require_once '../vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;

// use Illuminate\Support\Facades\DB;

// use App\Models\Banner;
// use Illuminate\Support\Facades\Artisan;

class customSchdule{
    public function __construct()
    {

        // $a=new \Illuminate\Support\Facades\Artisan;
        Artisan::call('schedule:run');
        // $banner=include('..\app\Models\Banner.php');
        // Artisan::call('schedule:run');
        
    // $d=new \Illuminate\Http\DB;
    //    $banner= new \app\Models\Banner;
    //    $banner->all();
        //   $banner->create([
        //     'title'=>'2233',
        //     'type'=>555,
        //     'link'=>'ddddddd',
        //     'img_src'=>'aaaaaa'
        // ]);
        // App/Models/Banner::all();
    // $data=$d->statement('SELECT * FROM `banners`');
    // $data=DB::table('banners')->get();
    echo '$data';
    }
}

new customSchdule;