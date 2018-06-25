<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/20
 * Time: 9:38
 */
namespace App\Http\Controllers\Ucenter\H5\User\Tickets;

use Illuminate\Support\Collection;
use library\Service\Contst\Common\RechangeConst;

trait RechangeTraits {


    public function formatRechangeBatch(Collection $items){
        return $items->each(function ($item){


        });
    }
}