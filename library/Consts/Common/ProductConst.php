<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/20
 * Time: 14:56
 */

namespace library\Service\Contst\Common;
class ProductConst{

    const HOTEL = 1;//酒店
    const TICKET = 2;//票务
    const CATES = 3;//餐饮
    const HOMESTAY = 4;//民宿

    const HOTEL_DESC = '酒店';
    const TICKET_DESC = '票务';
    const CATES_DESC = '餐饮';
    const HOMESTAY_DESC = '民宿';

    const ADULT = 1;//成人
    const STUDENT = 2;//学生
    const CHILD = 3; //儿童

    const ADULT_DESC = '成人';
    const STUDENT_DESC = '学生';
    const CHILD_DESC = '儿童';

    public static function getUserTypeItem($v = ''){
        $data =  [
            self::ADULT=>self::ADULT_DESC,
            self::STUDENT=>self::STUDENT_DESC,
            self::CHILD=>self::CHILD_DESC,
        ];
        return !empty($v) ? $data[$v] : $data;
    }
}