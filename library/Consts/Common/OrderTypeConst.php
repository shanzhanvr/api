<?php
namespace library\Service\Contst\Common;


class OrderTypeConst {

    const ADULT = 1;//成人
    const STUDENT = 2;//学生
    const CHILD = 3; //儿童

    const ADULT_DESC = '成人';
    const STUDENT_DESC = '学生';
    const CHILD_DESC = '儿童';

    const NON_PAYMENT = 0;
    const YES_PAYMENT = 1;
    const PEN_PAYMENT = 2;
    const NON_PAYMENT_DESC = '未付款';
    const YES_PAYMENT_DESC = '已付款';
    const PEN_PAYMENT_DESC = '待付款';

    const NO_CHECKED=0;//未核验
    const SCAN_CHECKED=1;//扫码核验
    const IEMI_CHECKED = 2;//串码核验

    const NO_CHECKED_DESC='未核验';
    const SCAN_CHECKED_DESC='扫码核验';
    const IEMI_CHECKED_DESC='串码核验';

    public static function getUserTypeItem($v = ''){
        $data =  [
                self::ADULT=>self::ADULT_DESC,
                self::STUDENT=>self::STUDENT_DESC,
                self::CHILD=>self::CHILD_DESC,
        ];
        return !empty($v) ? $data[$v] : $data;
    }
    public static function getOrderStatusItem($v = ''){
        $data = [
                self::NON_PAYMENT=>self::NON_PAYMENT_DESC,
                self::YES_PAYMENT=>self::YES_PAYMENT_DESC,
                self::PEN_PAYMENT=>self::PEN_PAYMENT_DESC
        ];
        return !empty($v) ? $data[$v] : $data;
    }

    public static function getCheckedTypeByItem($v = ''){
        $data = [
                self::NO_CHECKED => self::NO_CHECKED_DESC,
                self::SCAN_CHECKED=>self::SCAN_CHECKED_DESC,
                self::IEMI_CHECKED=>self::IEMI_CHECKED_DESC
        ];
        return !empty($v) ? $data[$v] : $data;
    }
}