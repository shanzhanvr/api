<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/19
 * Time: 14:44
 */
namespace App\Api\Ucenter\V1\Bls\Model;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model{

    public $connection = 'db_vr_ucenter';
}