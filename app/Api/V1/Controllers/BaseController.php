<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 11:43
 */
namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use library\Response\JsonResponse;


class BaseController extends Controller {
    use Helpers;

    protected $validateRule = [];
    protected $storeValidateRule = null;
    protected $updateValidateRule = null;

    protected $errorMsg = [];
    protected $storeErrorMsg = null;
    protected $updateErrorMsg = null;

    public function __construct() {

    }
    public function success($data = []){
        return JsonResponse::success($data);
    }
    public function error($code = 0,$message = 'error',$data = []){
        return JsonResponse::error($code,$message,$data);
    }
    public function getFenFormYuan($value)
    {
        return $value / 100;
    }
    public function getYuanFromFen($value)
    {
        return $value * 100;
    }
    public function getSumamount($amount,$amount2){
        return (int)($amount+$amount2);
    }
}