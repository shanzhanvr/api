<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 17:24
 */
namespace App\Api\V1\Bls\Merchant\Model;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model{

    protected $connection = 'db_vr_merchant';

}