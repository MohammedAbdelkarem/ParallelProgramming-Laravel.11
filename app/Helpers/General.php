<?php

use App\Models\User;
use App\Models\Story;
use App\Models\Answer;
use App\Models\Banner;
use Nette\Utils\Random;
use App\Enums\LevelEnum;
use App\Enums\MediaTypeEnum;
use App\Constants\ModelPaths;
use App\Constants\MediaCollection;
use App\Enums\ActorTypeEnum;
use App\Models\Administration\Profile\AdminProfile;
use App\Models\CustomClearenceCompany;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\DriverCompany;
use App\Models\System\SystemSetting;
use App\Models\Wallet;
use Illuminate\Support\Facades\Config;
use App\Services\System\SystemSettingService;

// if (!function_exists('generateEmail')) {
//     function generateEmail()
//     {
//         do {
//             $year = date('Y');
//             $month = date('m');
//             $randomNumber = random_int(00, 99);
//             $email =  $year . $month . $randomNumber . '@friendApp.com';
//         } while (User::where('email', $email)->exists());

//         return $email;
//     }
// }

if (!function_exists('generatePassword')) {
    function generatePassword()
    {
        $year = date('Y');
        $month = date('m');
        $randomNumber = random_int(00, 99);
        $password = $year . $month . $randomNumber . Random::generate(15);
        return $password;
    }
}

if (!function_exists('customizePaginationData')) {
    function customizePaginationData($data)
    {
        $paginationData = [
            'total'          => $data['total'] ?? null,
            'per_page'       => $data['per_page'] ?? null,
            'first_page_url' => $data['first_page_url'] ?? null,
            'prev_page_url'  => $data['prev_page_url'] ?? null,
            'current_page'   => $data['current_page'] ?? null,
            'next_page_url'  => $data['next_page_url'],
            'last_page_url'  => $data['last_page_url'] ?? null,
            // 'from'           => $data['from'] ?? null,
            // 'last_page'      => $data['last_page'] ?? null,
            // 'links'          => $data['links'],
            // 'path'           => $data['path'],
            // 'to'             => $data['to'] ?? null,
        ];

        return $paginationData;
    }
}

if (!function_exists('selectRandomElement')) {
    function selectRandomElement($values, $weights)
    {

        $weightedValues = array_combine($values, $weights);
        $rand = mt_rand(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $value => $weight) {
            $rand -= $weight;
            if ($rand <= 0) {
                return $value;
            }
        }
    }
}

if (!function_exists('generateRandomNumber')) {
    function generateRandomNumber(int $numberOfDigits): string
    {
        // Ensure that the first digit is not 0 to prevent octal interpretation
        $firstDigit = mt_rand(1, 9);
        $number = (string)$firstDigit;

        // Generate the remaining digits
        for ($i = 1; $i < $numberOfDigits; $i++) {
            $number .= mt_rand(0, 9);
        }

        return $number;
    }
}

if (!function_exists('prepareTranslatableData')) {
    function prepareTranslatableData(array $validatedData): array
    {
        $translatableData = [];
        $supportedLocales = Config::get('app.available_locales', []);

        foreach ($validatedData as $key => $value) {
            if (strpos($key, '_') !== false) {
                [$field, $locale] = explode('_', $key, 2);

                if (in_array($locale, $supportedLocales)) {
                    $translatableData[$field][$locale] = $value;
                } else {
                    $translatableData[$key] = $value;
                }
            } else {
                $translatableData[$key] =  $value;
            }
        }
        return $translatableData;
    }
}

if (!function_exists('decodeStringToArray')) {
    function decodeStringToArray(string $string): array
    {
        $array = [];
        if (is_string($string)) {
            $array = json_decode(str_replace("'", '"', $string), true);
            if (!is_array($array)) $array = [];
        }
        return $array;
    }
}


if (!function_exists('getMediaType')) {
    function getMediaType($mime_type)
    {
        if ((strpos($mime_type, 'image') !== false)) {
            return MediaTypeEnum::IMAGE;
        } elseif ((strpos($mime_type, 'video') !== false)) {
            return MediaTypeEnum::VIDEO;
        } else {
            return MediaTypeEnum::PDF;
        }
    }
}

if (!function_exists('getOrPaginate')) {
    function getOrPaginate($items, $data)
    {
        // dd($data);
        $items = (isset($data['per_page']))
            ? $items->paginate($data['per_page'])
            : $items->get();

        return $items;
    }
}


if (!function_exists('mediaCollectionByContxt')) {
    function mediaCollectionByContxt($model_path)
    {
        $data = [
            LevelEnum::STORY          => MediaCollection::STORY_COLLECTION,
            LevelEnum::BANNER         => MediaCollection::BANNER_COLLECTION,
            LevelEnum::USER         => MediaCollection::USER_COLLECTION,
            LevelEnum::E_LEVEL         => MediaCollection::E_LEVEL_COLLECTION,
            LevelEnum::C_LEVEL         => MediaCollection::C_LEVEL_COLLECTION,
        ];

        return $data[$model_path] ?? null;
    }
}


if (!function_exists('getModel')) {
    function getModel($model_path)
    {
        // dd($model_path);
        $data = [
            ActorTypeEnum::DRIVER->value                => Driver::class,
            ActorTypeEnum::DRIVER_COMPANY->value                => DriverCompany::class,
            ActorTypeEnum::CUSTOM_CLEARENCE_COMPANY->value                => CustomClearenceCompany::class,
            ActorTypeEnum::CUSTOMER->value                => Customer::class,
        ];

        // dd($data);
        return $data[$model_path] ?? null;
    }
}


if (!function_exists('getModelName')) {
    function getModelName($model_path)
    {
        $data = [
            ModelPaths::Story          => LevelEnum::STORY,
            ModelPaths::Banner         => LevelEnum::BANNER,
            ModelPaths::User         => LevelEnum::USER,
            ModelPaths::ELevel         => LevelEnum::E_LEVEL,
            ModelPaths::CLevel         => LevelEnum::C_LEVEL,
        ];

        return $data[$model_path] ?? null;
    }
}

if (!function_exists('getModelByPath')) {
    function getModelByPath($model_path)
    {
        $data = [
            ModelPaths::Driver         => Driver::class,
            ModelPaths::DriverCompany          => DriverCompany::class,
            ModelPaths::CustomClearenceCompany          => CustomClearenceCompany::class,
            ModelPaths::Customer         => Customer::class,
            ModelPaths::AdminProfile         => AdminProfile::class,
            ModelPaths::User         => User::class,
        ];

        return $data[$model_path] ?? null;
    }
}

if (!function_exists('getActorByModel')) {
    function getActorByModel($model_path)
    {
        $data = [
            
            AdminProfile::class              => ActorTypeEnum::ADMIN->value,
            User::class              => ActorTypeEnum::USER->value,
        ];

        return $data[$model_path] ?? null;
    }
}

if (!function_exists('maskString')) {
    function maskString($string, $visibleCharsLength = 5, $maskChar = '*')
    {
        $visiblePart = substr($string, 0, $visibleCharsLength);
        $maskedPart = str_repeat($maskChar, strlen($string) - $visibleCharsLength);
        return "$visiblePart$maskedPart";
    }
}
if (!function_exists('price_per_km')) {
    function price_per_km()
    {
        return intval(SystemSetting::find(1)->value);
    }
}
if (!function_exists('order_range')) {
    function order_range()
    {
        return intval(SystemSetting::find(2)->value);
    }
}
if (!function_exists('platform_ratio_percentage')) {
    function platform_ratio_percentage()
    {
        return intval(SystemSetting::find(3)->value);
    }
}
if (!function_exists('tax_value_percentage')) {
    function tax_value_percentage()
    {
        return intval(SystemSetting::find(4)->value);
    }
}

if (!function_exists('admin_wallet_id')) {
    function admin_wallet_id()
    {
        return 1;
    }
}

if (!function_exists('isForSocket')) {
    /**
     * Check if the current execution is for a WebSocket event or authorization.
     *
     * @return bool
     */
    function isForSocket(): bool
    {
        return app()->runningInConsole() || request()->is('broadcasting/auth');
    }
}

if (!function_exists('generateReferralCode')) {
    /**
     * Generate a referral code.
     *
     * @return string
     */
    function generateReferralCode() 
    {
        $hash = strtoupper(substr(md5(microtime(true)), 0, 6));
        return "DR-" . $hash;
    }
}