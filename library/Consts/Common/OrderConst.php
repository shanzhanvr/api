<?php
namespace library\Service\Contst\Common;


class OrderConst {

    //支付常量
    const PAYMENT_NO = 0;
    const PAYMENT_YES = 1;

    const PAYMENT_NO_DESC = '待付款';
    const PAYMENT_YES_DESC = '已付款';

    //核验常量
    const CHECKED_ING=0;//待核验
    const CHECKED_FAIL=1;//核验成功
    const CHECKED_SUCCESS = 2;//核验失败

    const CHECKED_ING_DESC='待核验';//待核验
    const CHECKED_FAIL_DESC='核验失败';//核验成功
    const CHECKED_SUCCESS_DESC = '核验成功';//核验失败

    //订单常量
    const CONFIRM_NO = 0;//待确认
    const CONFIRM_YES = 1;//已确认
    const CONFIRM_APPLY_REUND = 2;//申请退款
    const CONFIRM_APPLY_REFUSE = 3;//拒绝退款
    const CONFIRM_APPLY_SUCCESS = 4;//拒绝退款

    //核验类型
    const CHECK_TYPE_IMIE = 1;//串码
    const CHECK_TYPE_QCODE = 2;//扫码

    const CHECK_TYPE_IMIE_DESC='串码';
    const CHECK_TYPE_QCODE_DESC='扫码';



    public static function getCheckedItem($v = ''){
        $data = [
                self::CHECKED_ING=>self::CHECKED_ING_DESC,
                self::CHECKED_FAIL=>self::CHECKED_FAIL_DESC,
                self::CHECKED_SUCCESS=>self::CHECKED_SUCCESS_DESC
        ];
        return !empty($v) ? $data[$v] : $data;
    }

    public static function getCheckedTypeByItem($v = ''){
        $data = [
                self::CHECK_TYPE_IMIE => self::CHECK_TYPE_IMIE_DESC,
                self::CHECK_TYPE_QCODE=>self::CHECK_TYPE_QCODE_DESC,
        ];
        return !empty($v) ? $data[$v] : $data;
    }
}