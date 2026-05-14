<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'fcm' => [
        'project_id' => env('FCM_PROJECT_ID'),
        'credentialsPath' => storage_path(env('FCM_CREDENTIALS_PATH')),
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'session_id' => env('WHATSAPP_SESSION_ID'),
    ],  

    'google' => [
        'maps_key' => env('GOOGLE_MAPS_KEY'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_WHATSAPP_FROM'),
        'ms_sid' => env('TWILIO_MESSAGING_SERVICE_SID'),
        'template_sid' => env('TWILIO_TEMPLATE_SID'),
    ],

    'rabet' => [
        /** Reference: logisti (sandbox) vs rabet (live) — actual hosts go in RABET_WASL_URL / RABET_BAYAN_URL. */
        'environment' => env('RABET_ENVIRONMENT', 'logisti'),
        /**
         * When true: reject offers if Bayan cannot return max load, or driver has no plate / company sends no plate when a shipment capacity check applies.
         * Set true in production .env after Logisti/Rabet credentials are stable.
         */
        'strict_vehicle_capacity' => filter_var(env('RABET_STRICT_VEHICLE_CAPACITY', false), FILTER_VALIDATE_BOOLEAN),
        'app_id'    => env('RABET_APP_ID'),
        'app_key'   => env('RABET_APP_KEY'),
        'client_id' => env('RABET_CLIENT_ID'),
        /** If set, Bayan HTTP uses these instead of RABET_APP_* (Elm staging keys often differ from Wasl / Logisti). */
        'bayan_app_id'  => env('RABET_BAYAN_APP_ID'),
        'bayan_app_key' => env('RABET_BAYAN_APP_KEY'),
        /** Optional UUID for Elm Bayan only; if unset, RABET_CLIENT_ID is used unless it equals RABET_APP_ID (then omitted). */
        'bayan_client_id' => env('RABET_BAYAN_CLIENT_ID'),
        'wasl_url'  => env('RABET_WASL_URL'),
        'bayan_url' => env('RABET_BAYAN_URL'),
        /** Legacy manifest + vehicle lookup (RABET_APP_* headers). If empty, falls back to bayan_url (same gateway as Wasl). Elm staging (elm.sa) does not route /bayan/manifest — set this to your Logisti base origin. */
        'bayan_legacy_url' => env('RABET_BAYAN_LEGACY_URL'),
        /** Seconds for Wasl/Bayan HTTP client (openapi / slow gateways). */
        'http_timeout' => (int) env('RABET_HTTP_TIMEOUT', 45),
        /**
         * true = Wasl per openapi-definition.json (EFF: /eff/v1/drivers|vehicles|trips).
         * false = legacy paths (/carrier/driver, …) if your gateway still exposes them.
         */
        'wasl_use_eff' => filter_var(env('RABET_WASL_USE_EFF', false), FILTER_VALIDATE_BOOLEAN),
        /** Optional Hijri DOB (YYYY-MM-DD) when DB only has Gregorian; required by EFF driver DTO. */
        'driver_hijri_dob_override' => env('RABET_DRIVER_HIJRI_DOB'),
        /** Bayan “manifest” style paths (gateway). Official Bayan OpenAPI uses /api/v1/carrier/trip — see BayanService. */
        'bayan_manifest_prefix' => trim(env('RABET_BAYAN_MANIFEST_PREFIX', 'bayan'), '/'),
        'bayan_vehicle_path_template' => env('RABET_BAYAN_VEHICLE_PATH_TEMPLATE', 'bayan/vehicle/%s'),
        /** Elm Bayan: POST /api/v1/freight-forwarder/trip (Postman collection). false = legacy /bayan/manifest only. */
        'bayan_use_freight_forwarder' => filter_var(env('RABET_BAYAN_USE_FREIGHT_FORWARDER', false), FILTER_VALIDATE_BOOLEAN),
        'bayan_ff_base_path' => trim(env('RABET_BAYAN_FF_BASE_PATH', 'api/v1/freight-forwarder'), '/'),
        'bayan_ff_carrier_type' => env('RABET_FF_CARRIER_TYPE', 'COMPANY'),
        /** National / commercial MOI for carrier (required when FF sync is enabled). */
        'bayan_ff_carrier_moi' => env('RABET_FF_CARRIER_MOI'),
        'bayan_ff_good_type_id' => (int) env('RABET_FF_GOOD_TYPE_ID', 55),
        /** true = resolve goodTypeId from GET good-types: prefer RABET_FF_GOOD_TYPE_ID if listed, else first id. */
        'bayan_ff_good_type_from_lookup' => filter_var(env('RABET_FF_GOOD_TYPE_FROM_LOOKUP', false), FILTER_VALIDATE_BOOLEAN),
        /** When identity does not start with 1 or 2, send this identityType (Elm usually requires the field). */
        'bayan_ff_default_driver_identity_type' => (int) env('RABET_FF_DEFAULT_DRIVER_IDENTITY_TYPE', 1),
        /** Force driver identityType (overrides auto / default). */
        'bayan_ff_driver_identity_type' => env('RABET_FF_DRIVER_IDENTITY_TYPE'),
        /** Staging: 10-digit id sent to Bayan instead of DB (Elm rejects checksum-invalid test numbers like 4444444444). */
        'bayan_ff_override_driver_identity_for_test' => env('RABET_FF_OVERRIDE_DRIVER_IDENTITY_FOR_TEST'),
        /** Fallback Arabic lines when order only has lat,lng (Elm rejects coordinate-only addresses in some environments). */
        'bayan_ff_address_sender_fallback'    => env('RABET_FF_ADDRESS_SENDER_FALLBACK', 'الرياض - مستودع الاستلام'),
        'bayan_ff_address_recipient_fallback' => env('RABET_FF_ADDRESS_RECIPIENT_FALLBACK', 'جدة - مستودع التسليم'),
        /** receivingLocation / deliveryLocation address (warehouses); if empty, same as sender/recipient address lines. */
        'bayan_ff_receiving_location_address' => env('RABET_FF_RECEIVING_LOCATION_ADDRESS'),
        'bayan_ff_delivery_location_address'  => env('RABET_FF_DELIVERY_LOCATION_ADDRESS'),
        /** When customer has no display name, use this before "CargoX Customer". */
        'bayan_ff_default_shipper_name' => env('RABET_FF_DEFAULT_SHIPPER_NAME'),
        /** When order has no dst_reciver_name, use this before falling back to shipper name. */
        'bayan_ff_default_recipient_name' => env('RABET_FF_DEFAULT_RECIPIENT_NAME'),
        /** Days after receivedDate for expectedDeliveryDate (Elm / Postman samples often use 1). */
        'bayan_ff_expected_delivery_offset_days' => max(1, (int) env('RABET_FF_EXPECTED_DELIVERY_OFFSET_DAYS', 2)),
        'bayan_ff_unit_id' => (int) env('RABET_FF_UNIT_ID', 1),
        'bayan_ff_plate_type_id' => (int) env('RABET_FF_PLATE_TYPE_ID', 2),
        'bayan_ff_sender_city_id' => (int) env('RABET_FF_SENDER_CITY_ID', 1),
        'bayan_ff_recipient_city_id' => (int) env('RABET_FF_RECIPIENT_CITY_ID', 2),
        'bayan_ff_plate_right' => env('RABET_FF_PLATE_RIGHT_LETTER', 'أ'),
        'bayan_ff_plate_middle' => env('RABET_FF_PLATE_MIDDLE_LETTER', 'ص'),
        'bayan_ff_plate_left' => env('RABET_FF_PLATE_LEFT_LETTER', 'م'),
        'bayan_ff_payment_method_id' => (int) env('RABET_FF_PAYMENT_METHOD_ID', 1),
        'bayan_ff_customer_type_id' => (int) env('RABET_FF_CUSTOMER_TYPE_ID', 1),
        'bayan_ff_item_quantity' => max(1, (int) env('RABET_FF_ITEM_QUANTITY', 1)),
        /** Elm PUT /trip/waybill/cancel — reason lookup id from Bayan (staging default 1 until you map real ids). */
        'bayan_ff_cancel_waybill_reason_id' => (int) env('RABET_FF_CANCEL_WAYBILL_REASON_ID', 1),
        /** Used when building actualDeliveryDate for PUT waybill/close (Elm validates against trip dates). */
        'bayan_ff_actual_delivery_timezone' => env('RABET_FF_ACTUAL_DELIVERY_TIMEZONE', 'Asia/Riyadh'),
        /** When true: PUT waybill/close sends expectedDeliveryDate from GET trip (Elm staging often rejects other dates). Set false to clamp "today" between received and expected. */
        'bayan_ff_close_use_expected_delivery_date' => filter_var(env('RABET_FF_CLOSE_USE_EXPECTED_DELIVERY_DATE', true), FILTER_VALIDATE_BOOLEAN),
    ],

    'zatca' => [
        'organization_name'   => env('ZATCA_ORGANIZATION_NAME'),
        'tax_number'          => env('ZATCA_TAX_NUMBER'),
        'registration_number' => env('ZATCA_REGISTRATION_NUMBER'),
        'street_name'         => env('ZATCA_STREET_NAME'),
        'building_number'     => env('ZATCA_BUILDING_NUMBER'),
        'plot_identification' => env('ZATCA_PLOT_IDENTIFICATION'),
        'city_subdivision'    => env('ZATCA_CITY_SUBDIVISION'),
        'city'                => env('ZATCA_CITY'),
        'postal_code'         => env('ZATCA_POSTAL_CODE'),
        'private_key'         => env('ZATCA_PRIVATE_KEY'),
        'certificate'         => env('ZATCA_CERTIFICATE'),
        'secret'              => env('ZATCA_SECRET'),
    ],

    'zoho_books' => [
        'client_id'          => env('ZOHO_CLIENT_ID'),
        'client_secret'      => env('ZOHO_CLIENT_SECRET'),
        'refresh_token'      => env('ZOHO_REFRESH_TOKEN'),
        'organization_id'    => env('ZOHO_ORGANIZATION_ID'),
        'default_customer_id'=> env('ZOHO_DEFAULT_CUSTOMER_ID'),
        'api_url'            => env('ZOHO_API_URL',      'https://www.zohoapis.sa/books/v3'),
        'accounts_url'       => env('ZOHO_ACCOUNTS_URL', 'https://accounts.zoho.sa/oauth/v2/token'),
        /** When true, sync invoices to Zoho outside production (staging / local QA). */
        'sync_enabled'       => filter_var(env('ZOHO_BOOKS_SYNC_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    ],

];
