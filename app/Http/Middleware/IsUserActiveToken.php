<?php

namespace App\Http\Middleware;

use App\Models\Oauth\OauthAccessTokens;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class IsUserActiveToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $userSerialize = serialize($user);
        $userUnserializeArray = (array)unserialize($userSerialize);

        $arrayKeys = array_keys($userUnserializeArray);
        foreach ($arrayKeys as $value) {
            if (strpos($value, 'accessToken') !== false) {
                $userAccessTokenArray = (array)$userUnserializeArray[$value];
                $arrayAccessKeys = array_keys($userAccessTokenArray);
                foreach ($arrayAccessKeys as $arrayAccessValue) {
                    if (strpos($arrayAccessValue, 'original') !== false) {
                        $userTokenId = $userAccessTokenArray[$arrayAccessValue]['id'];
                        
                        $checkToken = OauthAccessTokens::where([
                            ['id', '=', $userTokenId],
                            ['expires_at', '>', Carbon::now()]
                        ])->first();

                        if (!$checkToken) {
                            return response()->json([
                                'success' => false,
                                'message' => __('app.token.not_valid'),
                                'error' => __('app.token.expired'),
                            ]);
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
