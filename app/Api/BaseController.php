<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 11:43
 */
namespace App\Api;

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
        return bcadd(abs($amount),abs($amount2));
    }
    public function getSubAmount($amount,$amount2){
        return bcsub(abs($amount),abs($amount2));
    }
    public function compareAmount($amount,$amount2){
        return bccomp(abs($amount),abs($amount2));
    }

    /**
     * 格式化金额
     *
     * @param int $money
     * @param int $len
     * @param string $sign
     * @return string
     */
    function format_money($money, $len=2, $sign='￥'){
        $negative = $money > 0 ? '' : '-';
        $int_money = intval(abs($money));
        $len = intval(abs($len));
        $decimal = '';//小数
        if ($len > 0) {
            $decimal = '.'.substr(sprintf('%01.'.$len.'f', $money),-$len);
        }
        $tmp_money = strrev($int_money);
        $strlen = strlen($tmp_money);
        $format_money = '';
        for ($i = 3; $i < $strlen; $i += 3) {
            $format_money .= substr($tmp_money,0,3).',';
            $tmp_money = substr($tmp_money,3);
        }
        $format_money .= $tmp_money;
        $format_money = strrev($format_money);
        return $sign.$negative.$format_money.$decimal;
    }
}