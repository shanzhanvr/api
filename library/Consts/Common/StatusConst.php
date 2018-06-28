<?php
namespace library\Service\Contst\Common;


class StatusConst
{
    const ENABLED = 1;//是
    const BLOCKED = 0;//否

    const BLOCKED_DESC = '是';
    const ENABLED_DESC = '否';




    const TRADE_FAIL = 0;//充值失败
    const TRADE_ING = 1;//充值中
    const TRADE_SUCCESS = 2;//成功
    const TRADE_ERROR = 3;//系统异常

    const TRADETUPE_RECHANGE=1;
    const TRADETUPE_FORWARD=2;



    /**
     * 获取所有状态信息
     * @return string[]
     */
    public static function desc()
    {
        return [
            self::ENABLED => self::ENABLED_DESC,
            self::BLOCKED => self::BLOCKED_DESC
        ];
    }

    public static function getDescByItem($item)
    {
        return array_get(self::desc(), $item);
    }
}