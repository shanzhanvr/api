<?php
namespace library\Service\Contst\Common;


class RechangeConst {

    //充值类型
    const WECHANT = 1;//微信
    const ALIPAY = 2;//支付宝

    const WECHANT_DESC = '微信';
    const ALIPAY_DESC = '支付宝';

    //充值状态
    const RECHANGE_ACTION_STATUCT_FAIL = 0;
    const RECHANGE_ACTION_STATUCT_ING = 1;
    const RECHANGE_ACTION_STATUCT_SUCCESS = 2;
    const RECHANGE_ACTION_STATUCT_ERROR = 3;

    /**
     * 获取所有状态信息
     * @return string[]
     */
    public static function desc()
    {
        return [
            self::WECHANT => self::WECHANT,
            self::ALIPAY => self::ALIPAY
        ];
    }

    public static function getDescByItem($item)
    {
        return array_get(self::desc(), $item);
    }
}