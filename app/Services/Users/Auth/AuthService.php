<?php

namespace App\Services\Users\Auth;

use App\Constants\ExceptionMessages;
use App\Constants\MediaCollection;
use App\Enums\AccountStatusEnum;
use App\Enums\CustomerTypeEnum;
use App\Enums\DriverTypeEnum;
use App\Exceptions\ApiException;
use App\Models\BankInformation;
use App\Models\CommercialRegisteration;
use App\Models\CompanyAdditionalDoc;
use App\Models\CompanyCustomerPersonalInfo;
use App\Models\CustomAdditionalDoc;
use App\Models\CustomClearenceCompany;
use App\Models\CustomClearenceLicense;
use App\Models\CustomCommercialRegisteration;
use App\Models\Customer;
use App\Models\CustomFasehRegisteration;
use App\Models\CustomTaxCertificate;
use App\Models\Driver;
use App\Models\DriverCompany;
use App\Models\DriverInsurance;
use App\Models\DriverLicense;
use App\Models\DriverOperatingCard;
use App\Models\DriverPassport;
use App\Models\DriverPersonalInfo;
use App\Models\DriverResidencyProof;
use App\Models\DriverVehicleOwnership;
use App\Models\GovernorateAdditionalDoc;
use App\Models\GovernorateAuthorizedEmployee;
use App\Models\GovernorateMinisterialDecision;
use App\Models\GovernorateOfficialRegisteration;
use App\Models\InvoicesAddress;
use App\Models\JWTPersonalTokens;
use App\Models\Plan;
use App\Models\PromoCode;
use App\Models\SingleCustomerPersonalInfo;
use App\Models\TransportLicense;
use App\Models\User;
use App\Models\Users\Profile\ArchivedUser;
use App\Models\Users\Profile\LoginHistory;
use App\Models\Users\Profile\UserDevice;
use App\Models\VatCard;
use App\Services\Base\ContextService;
use App\Services\Driver\DriverService;
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

    public function loginUser($validatedData)
    {

        $user = User::where('phone_number' , $validatedData['phone_number'])
                        ->where('role_id' , 3)->first();
        
        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => config("app.env") == "local" ? (string) $otp->otp : $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
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
        if ($notiToken)
            $user->userDevices()->where('notification_token', $notiToken)->delete();
        
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
        $accessExpireIn = Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp;
        $refreshExpireIn = Carbon::now()->addMinutes(config('jwt.refresh_ttl'))->timestamp;

        $accessToken  = JWTAuth::customClaims([
            'exp'               => $accessExpireIn,
            'api_access'        => true,
            'refresh_access'    => false,
        ])->fromUser($user);
        $refreshToken = JWTAuth::customClaims([
            'exp'               => $refreshExpireIn,
            'api_access'        => false,
            'refresh_access'    => true,
        ])->fromUser($user);

        //Store Tokens In DB
        $accessTokenDB  = $this->jwtService->store($accessToken, null, $loginHistoryId);
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

        $accessToken  = JWTAuth::customClaims([
            'exp'               => $accessExpireIn,
            'otp_access'        => true,
            'api_access'        => false,
            'refresh_access'    => false,
        ])->fromUser($user);

        return [
            "access_token"      => $accessToken,
            "access_expire_in"  => $accessExpireIn,
        ];
    }
}
