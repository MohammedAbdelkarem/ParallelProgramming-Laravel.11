<?php

namespace App\Services;

use App\Models\JWTPersonalTokens;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTTokensService
{
    public function store($token, $related_to = null, $loginHistoryId = null)
    {
        $payload = JWTAuth::setToken($token)->getPayload()->toArray();

        $loginDeviceId = $payload['api_access'] ? $loginHistoryId : null;

        $claims = json_encode([
            'exp'               => $payload['exp'],
            'api_access'        => $payload['api_access'],
            'refresh_access'    => $payload['refresh_access'],
        ]);

        $token = JWTPersonalTokens::create([
            "user_id"       => $payload['sub'],
            "related_to"    => $related_to,
            "login_history" => $loginDeviceId,
            "token"         => $token,
            "claims"        => $claims,
            "expire_at"     => Carbon::parse($payload['exp'])->format('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    public function InvalidateTokenWithRelated($token)
    {
        JWTAuth::invalidate($token);
        $token = JWTPersonalTokens::where('token', $token->get())->first();
        if ($token) { // Check if token exists in DB
            if ($token->related_to == null) //Get the refresh from access
                $relatedToken = JWTPersonalTokens::where('related_to', $token->id)->first();
            else //Get the access from refresh
                $relatedToken = $token->access_token;

            if (json_decode($token->claims)->exp > time())
                $token->delete();

            if ($relatedToken) {
                if (json_decode($relatedToken->claims)->exp > time())
                    JWTAuth::invalidate(JWTAuth::setToken($relatedToken->token)->getToken());
                $relatedToken->delete();
            }
        }
    }

    public function InvalidateAllTokensByUserID($user_id)
    {
        JWTPersonalTokens::where("user_id", $user_id)->chunk(20, function ($tokens) {
            foreach ($tokens as $token) {
                $tokenToInvalidate = JWTAuth::setToken($token->token)->getToken();
                if (json_decode($token->claims)->exp > time())
                    JWTAuth::invalidate($tokenToInvalidate);
            }
        });

        //In one query
        JWTPersonalTokens::where('user_id', $user_id)->delete();
    }

    public function invalidateSessionByDevice($ids)
    {
        $tokensQuery = JWTPersonalTokens::query()
            ->where('user_id', auth()->id())
            ->whereIn('login_history', $ids)
            ->where("expire_at", ">=", Carbon::now())
            ->with('refresh_token');

        foreach ($tokensQuery->get() as $token) {
            if (json_decode($token->claims)->exp > time()) {
                JWTAuth::invalidate(JWTAuth::setToken($token->token)->getToken());
                JWTAuth::invalidate(JWTAuth::setToken($token->refresh_token->token)->getToken());
            }
        }

        $tokensQuery->delete();
    }
}
