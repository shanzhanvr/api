<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/8/27
 * Time: 16:05
 */

namespace App\Http\Middleware;

use Closure;

class ApiAgencyAuth {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        config(['jwt.user' => '\App\Api\Agency\V1\Bls\Model\AgencyModel']);    //重要用于指定特定model
        config(['auth.providers.users.model' => \App\Api\Agency\V1\Bls\Model\AgencyModel::class]);//重要用于指定特定model！！！！
        return $next($request);
    }
}