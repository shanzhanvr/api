<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/13
 * Time: 9:27
 */
namespace App\Api\V1\Transformers\Merchant;

/**该类为dingo api封装好**/
use League\Fractal\TransformerAbstract;

class MerchantTransformer extends TransformerAbstract{
    /***
     * 分开为了解耦
     * 数据字段选择
     * @param $lesson
     * @return array
     */
    public function transform($lesson) {
        /******隐藏数据库字段*****/
        return [
            'mobile' => $lesson->mobile,
            'customertype' => $lesson->customertype,
            'avatar' => $lesson->avatar,
            'status' => $lesson->status
        ];
    }
}