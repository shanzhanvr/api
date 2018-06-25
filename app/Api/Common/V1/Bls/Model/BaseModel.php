<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/21
 * Time: 19:08
 */
namespace App\Api\Common\V1\Bls\Model;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model{

    protected $connection = 'db_vr_common';
}