<?php
namespace library\Service\Contst\Common;


class StatusConst
{
    const ENABLED = 1;//是
    const BLOCKED = 0;//否

    const BLOCKED_DESC = '是';
    const ENABLED_DESC = '否';

    //账户中心
    const CODE_STATUS = 1; //验证码失效
    const CODE_SATUS = 2;  //验证码不相同
    const CODE_RECODES = 3; //用户记录
    const CODE_USERSTATUS = 1;  //用户状态
    const CODE_USERCOOD = 4;  //用户账户被冻结
    const CODE_USERAUTH= 6;   //用户认证
    const CODE_USERMONEY = 5;  //用户提现金额不足
    const CODE_USERPAY = 1;  //支付宝提现方式
    const CODE_USERPAYBANK = 2;  //网银提现方式
    const CODE_USERPAYING = 1;  ////提现中
    const CODE_WEIXINPAY = 1;     //微信充值
    const CODE_ALIPAY = 2;     //支付宝充值

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