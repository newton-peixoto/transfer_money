<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class AllowedToTransferMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try {
            $userId = \Auth::user()->id;
            
            if($userId == $request->payee) {
                throw new Exception("You can't transfer money to yourself! You should deposit it!", 400);
            }
    
            if(\Auth::user()->isShopKeeper()) {
                throw new Exception("ShopKeepers can't transfer money!", 400);
            }
            return $next($request);
        } catch ( Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() == 0 ? 500 : $e->getCode());
        }

        
    }
}
