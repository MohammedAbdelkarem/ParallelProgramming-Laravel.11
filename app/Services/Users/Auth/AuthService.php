<?php
namespace App\Services\Users\Auth;

use App\Models\JWTPersonalTokens;
use App\Models\User;
use App\Models\Users\Profile\LoginHistory;
use App\Models\Users\Profile\UserDevice;
use App\Services\Base\ContextService;
use App\Services\JWTTokensService;
use App\Services\MainService;
use App\Services\OTPService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class AuthService.
 */
class AuthService extends MainService
{
    public function __construct(
        protected OTPService $OTPService,
        protected JWTTokensService $jwtService,
        protected ContextService $contextService,
    ) {}

    public function register($validatedData)
    {
        // dd(9);
        //create the student
        $user = User::create([
            'phone_number' => $validatedData['phone_number'],
            'role_id'      => 3,
            'email'        => $validatedData['email'],
            // 'account_status' => AccountStatusEnum::PENDING->value,
        ]);

        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id"                => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }
    public function loginUser($validatedData)
    {

        $user = User::where('phone_number', $validatedData['phone_number'])
            ->where('role_id', 3)->first();

        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => config("app.env") == "local" ? (string) $otp->otp : $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id"                => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }

    public function activeSessions()
    {
        return LoginHistory::where('user_id', auth()->id())
            ->whereHas('token', function ($q) {
                $q->where('expire_at', '>', Carbon::now());
            })
            ->orderByDesc('created_at')
            ->get();
    }

    public function logoutSessions($ids)
    {
        $this->jwtService->invalidateSessionByDevice($ids);
    }

    public function logout($notiToken)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        $this->jwtService->InvalidateTokenWithRelated(JWTAuth::getToken());
        if ($notiToken) {
            $user->userDevices()->where('notification_token', $notiToken)->delete();
        }

    }

    public function logoutAllDevices()
    {
        $user = auth()->user();

        $this->jwtService->InvalidateAllTokensByUserID($user->id);
        UserDevice::where('user_id', $user->id)->delete();
    }

    public function refresh()
    {
        $loginHistoryId = JWTPersonalTokens::where('token', JWTAuth::getToken())
            ->first()?->access_token()->first()?->login_history;

        $this->jwtService->InvalidateTokenWithRelated(JWTAuth::getToken());
        $data['tokens'] = $this->generateTokens(auth()->user(), $loginHistoryId);
        return $data;
    }

    public function generateTokens($user, $loginHistoryId)
    {
        $accessExpireIn  = Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp;
        $refreshExpireIn = Carbon::now()->addMinutes(config('jwt.refresh_ttl'))->timestamp;

        $accessToken = JWTAuth::customClaims([
            'exp'            => $accessExpireIn,
            'api_access'     => true,
            'refresh_access' => false,
        ])->fromUser($user);
        $refreshToken = JWTAuth::customClaims([
            'exp'            => $refreshExpireIn,
            'api_access'     => false,
            'refresh_access' => true,
        ])->fromUser($user);

        //Store Tokens In DB
        $accessTokenDB = $this->jwtService->store($accessToken, null, $loginHistoryId);
        $this->jwtService->store($refreshToken, $accessTokenDB->id);

        return [
            "access_token"      => $accessToken,
            "refresh_token"     => $refreshToken,
            "access_expire_in"  => $accessExpireIn,
            "refresh_expire_in" => $refreshExpireIn,
        ];
    }

    /**
     * Generate Token for otp request only
     * No need to store the token into DB because it's only for otp verification
     */
    public function generateLoginToken($user)
    {
        $accessExpireIn = Carbon::now()->addMinutes(config('jwt.otp_ttl'))->timestamp;

        $accessToken = JWTAuth::customClaims([
            'exp'            => $accessExpireIn,
            'otp_access'     => true,
            'api_access'     => false,
            'refresh_access' => false,
        ])->fromUser($user);

        return [
            "access_token"     => $accessToken,
            "access_expire_in" => $accessExpireIn,
        ];
    }
}
