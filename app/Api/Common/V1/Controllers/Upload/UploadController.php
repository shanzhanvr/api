<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/22
 * Time: 11:15
 */
namespace App\Api\Common\V1\Controllers\Upload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller {

    public function upFile(Request $request){
        $file = $request->file('file');
        Log::info($file);
        $up_dir = '/upload/images/home/'.date('Y',time()).'/'.date('m',time());
        if(!is_dir($up_dir)){
            mkdir($up_dir,0777,true);
        }
        strtoupper(substr(PHP_OS,0,3))==='WIN' ?
            $up_dir = '/upload/images/home/'.date('Y',time()).'/'.date('m',time()):
            $up_dir = '/var/www/html/images/'.date('Y',time()).'/'.date('m',time());
        if(!is_dir($up_dir)){
            mkdir($up_dir,0777,true);
        }
        $fileType = ['pjpeg','jpeg','jpg','gif','bmp','png'];
        //获取文件的原文件名 包括扩展名
        $originName= $file->getClientOriginalName();//源文件名
        //获取文件的扩展名
        $extendName=$file->getClientOriginalExtension();//扩展名
        //获取文件的类型
        $fileType=$file->getClientMimeType();
        //获取文件的绝对路径，但是获取到的在本地不能打开
        $path=$file->getRealPath();
        Log::info($originName.$extendName.$fileType);
        $new_name = time().uniqid();
//        $new_file = $up_dir.'/'.$new_name.'.'.$type;

//        $base64_1 = str_replace($result[1],'', $base64_img);
    }


}