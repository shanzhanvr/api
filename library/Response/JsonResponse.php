<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 15:05
 */
namespace library\Response;

class JsonResponse implements Contract{

    public static function success($data = []) {

        return json_encode(['code'=>200,'message'=>'success','data'=>$data]);
        // TODO: Implement success() method.
    }

    public static function error($code, $msg = '',$data = []) {
        // TODO: Implement error() method.
        return json_encode(['code'=>$code,'message'=>$msg,'data'=>$data]);
    }

    public static function jsonp($callback) {
        // TODO: Implement jsonp() method.
    }
}