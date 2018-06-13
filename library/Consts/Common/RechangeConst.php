<?php
namespace library\Service\Contst\Common;


class RechangeConst
{
    const WECHANT = 1;//微信
    const ALIPAY = 2;//支付宝

    const WECHANT_DESC = '微信';
    const ALIPAY_DESC = '支付宝';
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