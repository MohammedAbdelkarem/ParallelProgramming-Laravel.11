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
        protected DriverService $driverService,
    ) {}
    public function registerSaudiDriver($validatedData)
    {
        //check the parent phone number if archived
        if (ArchivedUser::where('phone_number', $validatedData["phone_number"])->count() >= config("_custom.max_accounts_per_phone_number"))
            throw new ApiException(null, trans(ExceptionMessages::MSG_PHONE_NUMBER_USED_MANY_TIMES), 400);

        //create the student
        $user = User::create([
            'phone_number' => $validatedData['phone_number'],
            'role_id' => 3,
            'email' => $validatedData['email'],
            'account_status' => AccountStatusEnum::PENDING,
        ]);

        $saudiDriver = Driver::create([
            'user_id' => $user->id,
            'driver_type' => DriverTypeEnum::SINGLE_SAUDI,
        ]);

        // Create related records with nullable values
        $driverPersonalInfo = DriverPersonalInfo::create(['driver_id' => $saudiDriver->id]);
        $driverLicense = DriverLicense::create(['driver_id' => $saudiDriver->id]);
        $driverVehicleOwnership = DriverVehicleOwnership::create(['driver_id' => $saudiDriver->id]);
        $driverOperatingCard = DriverOperatingCard::create([
            'driver_id' => $saudiDriver->id,
            'operating_card_number' => $validatedData['operating_card_number']
        ]);
        $driverInsurance = DriverInsurance::create(['driver_id' => $saudiDriver->id]);
        $driverResidencyProof = DriverResidencyProof::create(['driver_id' => $saudiDriver->id]);
        
        uploadFileOnMedia($validatedData['national_card_front'] , $driverPersonalInfo, MediaCollection::DRIVER_NATIONAL_CARD_FRONT_COLLECTION);
        uploadFileOnMedia($validatedData['national_card_back'] , $driverPersonalInfo, MediaCollection::DRIVER_NATIONAL_CARD_BACK_COLLECTION);
        uploadFileOnMedia($validatedData['driving_license'] , $driverLicense, MediaCollection::DRIVER_DRIVING_LICENSE_COLLECTION);
        uploadFileOnMedia($validatedData['owner_card'] , $driverVehicleOwnership, MediaCollection::DRIVER_OWNER_CARD_COLLECTION);
        uploadFileOnMedia($validatedData['operating_card'] , $driverOperatingCard, MediaCollection::DRIVER_OPERATING_CARD_COLLECTION);
        uploadFileOnMedia($validatedData['insurance_card'] , $driverInsurance, MediaCollection::DRIVER_INSURANCE_CARD_COLLECTION);
        uploadFileOnMedia($validatedData['residency_card'] , $driverResidencyProof, MediaCollection::DRIVER_RESIDENCY_CARD_COLLECTION);
        uploadFileOnMedia($validatedData['profile_image'] , $saudiDriver, MediaCollection::DRIVER_PROFILE_IMAGE_COLLECTION);
        

        if($validatedData['referral_code'] != null)
        {
            $promoCode = PromoCode::where('referral_code', $validatedData['referral_code'])->first();

            if($promoCode)
            {
                $promoCode->invited_driver_id = $saudiDriver->id;
                $promoCode->save();
            }
        }

        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }
    public function registerSaudiDriverEmployee($validatedData)
    {
        //check the parent phone number if archived
        if (ArchivedUser::where('phone_number', $validatedData["phone_number"])->count() >= config("_custom.max_accounts_per_phone_number"))
            throw new ApiException(null, trans(ExceptionMessages::MSG_PHONE_NUMBER_USED_MANY_TIMES), 400);

        //create the student
        $user = User::create([
            'phone_number' => $validatedData['phone_number'],
            'role_id' => 3,
            'email' => $validatedData['email'],
            'account_status' => AccountStatusEnum::PENDING,
        ]);

        $saudiDriverEmployee = Driver::create([
            'user_id' => $user->id,
            'driver_type' => DriverTypeEnum::EMPLOYEE_SAUDI,
        ]);

        if(isset($validatedData['company_commercial_registration_number']))
        {
            $driver_company_id = $this->driverService->getDriverCompnayId($validatedData['company_commercial_registration_number']);
            $saudiDriverEmployee->driver_company_id = $driver_company_id;
        }
        elseif(isset($validatedData['driver_company_id']))
            $saudiDriverEmployee->driver_company_id = $validatedData['driver_company_id'];
        else
            $saudiDriverEmployee->custom_clearence_company_id = $validatedData['custom_clearence_company_id'];

        $saudiDriverEmployee->save();

        // Create related records with nullable values
        $driverPersonalInfo = DriverPersonalInfo::create(['driver_id' => $saudiDriverEmployee->id]);
        $driverLicense = DriverLicense::create(['driver_id' => $saudiDriverEmployee->id]);
        $driverVehicleOwnership = DriverVehicleOwnership::create(['driver_id' => $saudiDriverEmployee->id]);
        foreach($validatedData['operating_cards'] as $operatingCard)
        {
            $driverOperatingCard = DriverOperatingCard::create([
                'driver_id' => $saudiDriverEmployee->id,
                'operating_card_number' => $operatingCard['operating_card_number']
            ]);
            uploadFileOnMedia($operatingCard['file'], $driverOperatingCard, MediaCollection::DRIVER_OPERATING_CARD_COLLECTION);
        }
        $driverInsurance = DriverInsurance::create(['driver_id' => $saudiDriverEmployee->id]);
        $driverResidencyProof = DriverResidencyProof::create(['driver_id' => $saudiDriverEmployee->id]);
        
        uploadFileOnMedia($validatedData['national_card_front'] , $driverPersonalInfo, MediaCollection::DRIVER_NATIONAL_CARD_FRONT_COLLECTION);
        uploadFileOnMedia($validatedData['national_card_back'] , $driverPersonalInfo, MediaCollection::DRIVER_NATIONAL_CARD_BACK_COLLECTION);
        uploadFileOnMedia($validatedData['driving_license'] , $driverLicense, MediaCollection::DRIVER_DRIVING_LICENSE_COLLECTION);
        uploadFileOnMedia($validatedData['owner_card'] , $driverVehicleOwnership, MediaCollection::DRIVER_OWNER_CARD_COLLECTION);
        uploadFileOnMedia($validatedData['insurance_card'] , $driverInsurance, MediaCollection::DRIVER_INSURANCE_CARD_COLLECTION);
        uploadFileOnMedia($validatedData['residency_card'] , $driverResidencyProof, MediaCollection::DRIVER_RESIDENCY_CARD_COLLECTION);
        uploadFileOnMedia($validatedData['profile_image'] , $saudiDriverEmployee, MediaCollection::DRIVER_PROFILE_IMAGE_COLLECTION);
        

        if($validatedData['referral_code'] != null)
        {
            $promoCode = PromoCode::where('referral_code', $validatedData['referral_code'])->first();

            if($promoCode)
            {
                $promoCode->invited_driver_id = $saudiDriverEmployee->id;
                $promoCode->save();
            }
        }

        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }
    public function registerNonSaudiDriverEmployee($validatedData)
    {
        //check the parent phone number if archived
        if (ArchivedUser::where('phone_number', $validatedData["phone_number"])->count() >= config("_custom.max_accounts_per_phone_number"))
            throw new ApiException(null, trans(ExceptionMessages::MSG_PHONE_NUMBER_USED_MANY_TIMES), 400);

        //create the student
        $user = User::create([
            'phone_number' => $validatedData['phone_number'],
            'role_id' => 3,
            'email' => $validatedData['email'],
            'account_status' => AccountStatusEnum::PENDING,
        ]);

        $nonSaudiDriverEmployee = Driver::create([
            'user_id' => $user->id,
            'driver_type' => DriverTypeEnum::EMPLOYEE_NON_SAUDI,
        ]);

        if(isset($validatedData['driver_company_id']))
            $nonSaudiDriverEmployee->driver_company_id = $validatedData['driver_company_id'];
        else
            $nonSaudiDriverEmployee->custom_clearence_company_id = $validatedData['custom_clearence_company_id'];

        $nonSaudiDriverEmployee->save();

        // Create related records with nullable values
        $driverPersonalInfo = DriverPersonalInfo::create(['driver_id' => $nonSaudiDriverEmployee->id]);
        $driverPassport = DriverPassport::create(['driver_id' => $nonSaudiDriverEmployee->id]);
        $driverLicense = DriverLicense::create(['driver_id' => $nonSaudiDriverEmployee->id]);
        foreach($validatedData['operating_cards'] as $operatingCard)
        {
            $driverOperatingCard = DriverOperatingCard::create([
                'driver_id' => $nonSaudiDriverEmployee->id,
                'operating_card_number' => $operatingCard['operating_card_number']
            ]);
            uploadFileOnMedia($operatingCard['file'], $driverOperatingCard, MediaCollection::DRIVER_OPERATING_CARD_COLLECTION);
        }
        $driverInsurance = DriverInsurance::create(['driver_id' => $nonSaudiDriverEmployee->id]);
        
        
        uploadFileOnMedia($validatedData['passport_card'] , $driverPassport, MediaCollection::DRIVER_PASSPORT_CARD_COLLECTION);
        uploadFileOnMedia($validatedData['driving_license'] , $driverLicense, MediaCollection::DRIVER_DRIVING_LICENSE_COLLECTION);
        uploadFileOnMedia($validatedData['insurance_card'] , $driverInsurance, MediaCollection::DRIVER_INSURANCE_CARD_COLLECTION);
        uploadFileOnMedia($validatedData['profile_image'] , $nonSaudiDriverEmployee, MediaCollection::DRIVER_PROFILE_IMAGE_COLLECTION);
        // dd(9);

        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }
    public function registerSingleCustomer($validatedData)
    {
        //check the parent phone number if archived
        if (ArchivedUser::where('phone_number', $validatedData["phone_number"])->count() >= config("_custom.max_accounts_per_phone_number"))
            throw new ApiException(null, trans(ExceptionMessages::MSG_PHONE_NUMBER_USED_MANY_TIMES), 400);

        //create the student
        $user = User::create([
            'phone_number' => $validatedData['phone_number'],
            'role_id' => 4,
            'email' => $validatedData['email'],
            'account_status' => AccountStatusEnum::PENDING,
        ]);

        $customer = Customer::create([
            'user_id' => $user->id,
            'customer_type' => CustomerTypeEnum::SINGLE,
        ]);

        // Create related records with nullable values
        $singleCustomerPersonalInfo = SingleCustomerPersonalInfo::create(['customer_id' => $customer->id]);

        uploadFileOnMedia($validatedData['national_card_front'] , $singleCustomerPersonalInfo, MediaCollection::CUSTOMER_NATIONAL_CARD_FRONT_COLLECTION);
        uploadFileOnMedia($validatedData['national_card_back'] , $singleCustomerPersonalInfo, MediaCollection::CUSTOMER_NATIONAL_CARD_BACK_COLLECTION);
        uploadFileOnMedia($validatedData['profile_image'] , $customer, MediaCollection::CUSTOMER_PROFILE_IMAGE_COLLECTION);
        

        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }

    public function registerCompanyCustomer($validatedData)
    {
        //check the parent phone number if archived
        if (ArchivedUser::where('phone_number', $validatedData["phone_number"])->count() >= config("_custom.max_accounts_per_phone_number"))
        throw new ApiException(null, trans(ExceptionMessages::MSG_PHONE_NUMBER_USED_MANY_TIMES), 400);

        //create the student
        $user = User::create([
            'phone_number' => $validatedData['phone_number'],
            'role_id' => 4,
            'email' => $validatedData['email'],
            'account_status' => AccountStatusEnum::PENDING,
        ]);

        $customer = Customer::create([
            'user_id' => $user->id,
            'customer_type' => CustomerTypeEnum::COMPANY,
        ]);

        // Create related records with nullable values
        $companyCustomerPersonalInfo = CompanyCustomerPersonalInfo::create([
            'customer_id' => $customer->id,
            // 'operating_card_number' => $validatedData['operating_card_number'],
            'address_city' => $validatedData['address_city'],
            'address_street' => $validatedData['address_street'],
            'address_district' => $validatedData['address_district'],
            'iban' => $validatedData['iban'],
            'bank_name' => $validatedData['bank_name'],
            'additional_bank_info' => $validatedData['additional_bank_info'] ?? null,
        ]);

        uploadFileOnMedia($validatedData['commercial_registration_card'] , $companyCustomerPersonalInfo, MediaCollection::CUSTOMER_COMPANY_COMMERCIAL_CARD_COLLECTION);
        if(isset($validatedData['vat_card']))
        {
            uploadFileOnMedia($validatedData['vat_card'] , $companyCustomerPersonalInfo, MediaCollection::CUSTOMER_COMPANY_VAT_CARD_COLLECTION);
        }
        if(isset($validatedData['additional_docs']))
        {
            foreach($validatedData['additional_docs'] as $additional_doc)
            {
                $model = CompanyAdditionalDoc::create([
                    'customer_personal_id' => $companyCustomerPersonalInfo->id,
                    'doc_title' => $additional_doc['doc_title'],
                    'doc_description' => $additional_doc['doc_description'] ?? null,
                ]);
                uploadFileOnMedia($additional_doc['file'], $model, MediaCollection::CUSTOMER_COMPANY_ADDITIONAL_DOCUMENTS_COLLECTION);
            }
        }
        

        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }
    public function registerGovernorateCustomer($validatedData)
    {
        //check the parent phone number if archived
        if (ArchivedUser::where('phone_number', $validatedData["phone_number"])->count() >= config("_custom.max_accounts_per_phone_number"))
        throw new ApiException(null, trans(ExceptionMessages::MSG_PHONE_NUMBER_USED_MANY_TIMES), 400);

        //create the student
        $user = User::create([
            'phone_number' => $validatedData['phone_number'],
            'role_id' => 4,
            'email' => $validatedData['email'],
            'account_status' => AccountStatusEnum::PENDING,
        ]);

        $customer = Customer::create([
            'user_id' => $user->id,
            'customer_type' => CustomerTypeEnum::GOVERNMENT,
            'address' => $validatedData['address'],
        ]);

        // Create related records with nullable values
        $governorateOfficialRegistration = GovernorateOfficialRegisteration::create(['customer_id' => $customer->id]);
        $governorateMinisterialDecision = GovernorateMinisterialDecision::create(['customer_id' => $customer->id]);
        $governorateAuthorizedEmployee = GovernorateAuthorizedEmployee::create(['customer_id' => $customer->id]);
        
        
        uploadFileOnMedia($validatedData['governorate_official_registeration'] , $governorateOfficialRegistration, MediaCollection::GOVERNORATE_OFFICIAL_REGISTRATION_COLLECTION);
        uploadFileOnMedia($validatedData['governorate_ministerial_decision'] , $governorateMinisterialDecision, MediaCollection::GOVERNORATE_MINISTERIAL_DECISION_COLLECTION);
        uploadFileOnMedia($validatedData['governorate_authorized_employee'] , $governorateAuthorizedEmployee, MediaCollection::GOVERNORATE_AUTHORIZED_EMPLOYEE_COLLECTION);

        

        if(isset($validatedData['additional_docs']))
        {
            foreach($validatedData['additional_docs'] as $additional_doc)
            {
                $model = GovernorateAdditionalDoc::create([
                    'customer_id' => $customer->id,
                    'document_type' => $additional_doc['document_type'],
                    'license_number' => $additional_doc['license_number'],
                    'issue_date' => $additional_doc['issue_date'] ?? null,
                    'expiry_date' => $additional_doc['expiry_date'] ?? null,
                ]);
                uploadFileOnMedia($additional_doc['file'], $model, MediaCollection::GOVERNORATE_ADDITIONAL_DOCS_COLLECTION);
            }
        }
        

        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }
    public function registerDriverCompany($validatedData)
    {
        //check the parent phone number if archived
        if (ArchivedUser::where('phone_number', $validatedData["phone_number"])->count() >= config("_custom.max_accounts_per_phone_number"))
        throw new ApiException(null, trans(ExceptionMessages::MSG_PHONE_NUMBER_USED_MANY_TIMES), 400);

        $commerciaRegisterationNumber = $validatedData['commercial_registeration_number'];

        if(CommercialRegisteration::where('commercial_registration_number' , $commerciaRegisterationNumber)->exists())
        {
            $driverCompany = DriverCompany::whereHas('commercialRegisteration' , function($q) use($commerciaRegisterationNumber){
                $q->where('commercial_registration_number' , $commerciaRegisterationNumber);
            })->first();

            $user = User::find($driverCompany->user_id);

            $user->update([
                'phone_number' => $validatedData['phone_number'],
                'email' => $validatedData['email'],
            ]);

            // Create related records with nullable values
            $transportLicense = $driverCompany->transportLicense;
            $invoicesAddress = $driverCompany->invoicesAddress;
            $vatCard = $driverCompany->vatCard;
            $commercialRegisteration = $driverCompany->commercialRegisteration;
            $bankInformation = $driverCompany->bankInformation;

            uploadFileOnMedia($validatedData['transport_license'] , $transportLicense, MediaCollection::DRIVER_COMPANY_TRANSPORT_LICENSE_COLLECTION);
            uploadFileOnMedia($validatedData['vat_card'] , $vatCard, MediaCollection::DRIVER_COMPANY_VAT_CARD_COLLECTION);
            uploadFileOnMedia($validatedData['commercial_registeration'] , $commercialRegisteration, MediaCollection::DRIVER_COMPANY_COMMERCIAL_REGISTERATION_COLLECTION);
            uploadFileOnMedia($validatedData['invoices_address'] , $invoicesAddress, MediaCollection::DRIVER_COMPANY_INVOICES_ADDRESS_COLLECTION);
            uploadFileOnMedia($validatedData['bank_information'] , $bankInformation, MediaCollection::DRIVER_COMPANY_BANK_INFORMATION_COLLECTION);
            uploadFileOnMedia($validatedData['profile_image'] , $driverCompany, MediaCollection::DRIVER_COMPANY_PROFILE_IMAGE_COLLECTION);
        }
        else
        {
            //create the student
            $user = User::create([
                'phone_number' => $validatedData['phone_number'],
                'role_id' => 5,
                'email' => $validatedData['email'],
                'account_status' => AccountStatusEnum::PENDING,
            ]);

            $driverCompany = DriverCompany::create([
                'user_id' => $user->id,
            ]);
            

            // Create related records with nullable values
            $transportLicense = TransportLicense::create(['driver_company_id' => $driverCompany->id]);
            $invoicesAddress = InvoicesAddress::create(['driver_company_id' => $driverCompany->id]);
            $vatCard = VatCard::create(['driver_company_id' => $driverCompany->id]);
            $commercialRegisteration = CommercialRegisteration::create(['driver_company_id' => $driverCompany->id]);
            $bankInformation = BankInformation::create(['driver_company_id' => $driverCompany->id]);
            
            uploadFileOnMedia($validatedData['transport_license'] , $transportLicense, MediaCollection::DRIVER_COMPANY_TRANSPORT_LICENSE_COLLECTION);
            uploadFileOnMedia($validatedData['vat_card'] , $vatCard, MediaCollection::DRIVER_COMPANY_VAT_CARD_COLLECTION);
            uploadFileOnMedia($validatedData['commercial_registeration'] , $commercialRegisteration, MediaCollection::DRIVER_COMPANY_COMMERCIAL_REGISTERATION_COLLECTION);
            uploadFileOnMedia($validatedData['invoices_address'] , $invoicesAddress, MediaCollection::DRIVER_COMPANY_INVOICES_ADDRESS_COLLECTION);
            uploadFileOnMedia($validatedData['bank_information'] , $bankInformation, MediaCollection::DRIVER_COMPANY_BANK_INFORMATION_COLLECTION);
            uploadFileOnMedia($validatedData['profile_image'] , $driverCompany, MediaCollection::DRIVER_COMPANY_PROFILE_IMAGE_COLLECTION);

        }
        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }
    public function registerCustomClearenceCompany($validatedData)
    {
        //check the parent phone number if archived
        if (ArchivedUser::where('phone_number', $validatedData["phone_number"])->count() >= config("_custom.max_accounts_per_phone_number"))
        throw new ApiException(null, trans(ExceptionMessages::MSG_PHONE_NUMBER_USED_MANY_TIMES), 400);

        //create the student
        $user = User::create([
            'phone_number' => $validatedData['phone_number'],
            'role_id' => 6,
            'email' => $validatedData['email'],
            'account_status' => AccountStatusEnum::PENDING,
        ]);

        $customClearenceCompany = CustomClearenceCompany::create([
            'user_id' => $user->id,
        ]);
        

        // Create related records with nullable values
        $commercialRegisteration = CustomCommercialRegisteration::create(['custom_company_id' => $customClearenceCompany->id]);
        $fasehDocument = CustomFasehRegisteration::create(['custom_company_id' => $customClearenceCompany->id]);
        $customCleareneceLicense = CustomClearenceLicense::create(['custom_company_id' => $customClearenceCompany->id]);

        uploadFileOnMedia($validatedData['commercial_registeration'] , $commercialRegisteration, MediaCollection::CUSTOM_CLEARENCE_COMPANY_COMMERCIAL_REGISTERATION_COLLECTION);
        uploadFileOnMedia($validatedData['faseh_document'] , $fasehDocument, MediaCollection::CUSTOM_CLEARENCE_COMPANY_FASEH_DOCUMENT_COLLECTION);
        uploadFileOnMedia($validatedData['custom_clearenece_license'] , $customCleareneceLicense, MediaCollection::CUSTOM_CLEARENCE_COMPANY_CLEARENCE_LICENSE_COLLECTION);


        if(isset($validatedData['tax_certificate']))
        {
            $taxCertificate = CustomTaxCertificate::create(['custom_company_id' => $customClearenceCompany->id]);
            uploadFileOnMedia($validatedData['tax_certificate'] , $taxCertificate, MediaCollection::CUSTOM_CLEARENCE_COMPANY_TAX_CERTIFICATE_COLLECTION);
        }

        if(isset($validatedData['additional_docs']))
        {
            foreach($validatedData['additional_docs'] as $additional_doc)
            {
                $model = CustomAdditionalDoc::create([
                    'custom_company_id' => $customClearenceCompany->id,
                    'doc_title' => $additional_doc['doc_title'],
                    'doc_description' => $additional_doc['doc_description'] ?? null,
                ]);
                uploadFileOnMedia($additional_doc['file'], $model, MediaCollection::CUSTOM_CLEARENCE_COMPANY_ADDITIONAL_DOCUMENTS_COLLECTION);
            }
        }

        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
        ];

        return $data;
    }
    public function loginDriver($validatedData)
    {

        $user = User::where('phone_number' , $validatedData['phone_number'])
                        ->where('role_id' , 3)->first();

        if($user->account_status == AccountStatusEnum::PENDING->value)
            return forbiddenFailure(['status' => $user->account_status], ExceptionMessages::MSG_ACCOUNT_IS_PENDING);
        else if($user->account_status == AccountStatusEnum::REJECTED->value)
            return forbiddenFailure(['status' => $user->account_status], ExceptionMessages::MSG_ACCOUNT_IS_REJECTED);
        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $driver = Driver::where('user_id', $user->id)->first();
        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "role_name" => "Driver",
            "driver_type" => $driver->driver_type,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
            'status' => $user->account_status
        ];

        return $data;
    }
    public function loginDriverCompany($validatedData)
    {

        $user = User::where('phone_number' , $validatedData['phone_number'])
                        ->where('role_id' , 5)->first();

        if($user->account_status == AccountStatusEnum::PENDING->value)
            return forbiddenFailure(['status' => $user->account_status], ExceptionMessages::MSG_ACCOUNT_IS_PENDING);
        else if($user->account_status == AccountStatusEnum::REJECTED->value)
            return forbiddenFailure(['status' => $user->account_status], ExceptionMessages::MSG_ACCOUNT_IS_REJECTED);
        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "role_name" => "Drivers Company",
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
            'status' => $user->account_status
        ];

        return $data;
    }
    public function loginCustomClearenceCompany($validatedData)
    {

        $user = User::where('phone_number' , $validatedData['phone_number'])
                        ->where('role_id' , 6)->first();

        if($user->account_status == AccountStatusEnum::PENDING->value)
            return forbiddenFailure(['status' => $user->account_status], ExceptionMessages::MSG_ACCOUNT_IS_PENDING);
        else if($user->account_status == AccountStatusEnum::REJECTED->value)
            return forbiddenFailure(['status' => $user->account_status], ExceptionMessages::MSG_ACCOUNT_IS_REJECTED);
        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "role_name" => "Custom Clearence Company",
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
            'status' => $user->account_status
        ];

        return $data;
    }
    public function loginCustomer($validatedData)
    {

        $user = User::where('phone_number' , $validatedData['phone_number'])
                        ->where('role_id' , 4)->first();

        if($user->account_status == AccountStatusEnum::PENDING->value)
            return forbiddenFailure(['status' => $user->account_status], ExceptionMessages::MSG_ACCOUNT_IS_PENDING);
        else if($user->account_status == AccountStatusEnum::REJECTED->value)
            return forbiddenFailure(['status' => $user->account_status], ExceptionMessages::MSG_ACCOUNT_IS_REJECTED);
        //Send otp
        $otp = $this->OTPService->createOTP($user->id, $validatedData['phone_number']);

        //Generate Token
        $token = $this->generateLoginToken($user);

        $data = [
            "otp"    => (string) $otp->otp, //TODO Check for remove
            "tokens" => $token,
            "role_name" => "Customer",
            "customer_type" => $user->customer->customer_type,
            "user"   => [
                "id" => $user->id,
                "user_phone_number" => $user->phone_number,
            ],
            'status' => $user->account_status
        ];

        return $data;
    }

    
    
    public function loginExample($validatedData)
    {

        $user = User::where('phone_number' , $validatedData['phone_number'])
                        ->where('role_id' , 4)->first();
        
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
