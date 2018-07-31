<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
require 'api/merchant/merchant.php';
require 'api/merchant/pay/rechange.php';
require 'api/authapi.php';
require 'api/common/sms/sms.php';
require 'api/common/area.php';
require 'api/panorama/panorama.php';
require  'api/merchant/pay/collfrees.php';
