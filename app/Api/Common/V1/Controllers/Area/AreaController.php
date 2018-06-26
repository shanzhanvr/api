<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/5
 * Time: 21:05
 */

namespace App\Api\Common\V1\Controllers\Area;

use App\Bls\Common\Model\AreaModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use library\Service\Cache\TMMemCacheMgr;

class AreaController extends Controller{


    /**
     * 地区联动
     */
    public function getArea(Request $request) {
        $tmem = TMMemCacheMgr::getInstance();
        echo $tmem->set('name','wujunjun');
        exit;
        if ($request->isMethod('post')) {
            return AreaModel::where('superiorId',$request->get('suid'))->get();
        }
        return AreaModel::where('superiorId','')->get();
    }

}