<?php

namespace App\Http\Middleware;

use Closure;

class ApiJwtAuth {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        config(['jwt.user' => '\App\Api\V1\Bls\Merchant\Model\MerchantModel']);    //重要用于指定特定model
        config(['auth.providers.users.model' => \App\Api\V1\Bls\Merchant\Model\MerchantModel::class]);//重要用于指定特定model！！！！
        return $next($request);
    }
}
