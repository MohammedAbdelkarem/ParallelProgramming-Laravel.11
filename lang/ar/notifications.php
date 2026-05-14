<?php

use App\Constants\NotificationMessages;
use App\Enums\Notifications\NotificationTypes;

return [

    /*
    |--------------------------------------------------------------------------
    | App notifications messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during App for various
    | notifications messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    //Enum Keys
    NotificationTypes::AUTH->value           => "اشعارات المصادقة",
    NotificationTypes::TRIP->value           => "اشعارات الرحلات",
    NotificationTypes::CHAT->value           => "اشعارات الدردشة",
    NotificationTypes::COMPLAINT->value      => "اشعارات الشكاوى",
    NotificationTypes::WALLET->value         => "اشعارات المحفظة",
    NotificationTypes::OTHER->value          => "اشعارات أخرى",
    NotificationTypes::CLEARENCE_TRANSACTION->value => "اشعارات معاملات التخليص",
    NotificationTypes::ORDER->value          => "اشعارات الطلبات",

    //
    "Suspend" => "حظر",
    "Unblock" => "Unblock",

    //Messages
    //Account
    NotificationMessages::LOGIN_TITLE   => "عملية تسجيل دخول جديدة",
    NotificationMessages::LOGIN_BODY    => "تمت عملية تسجيل دخول جديدة لحسابك من جهاز: :device من: :location",
    NotificationMessages::BAN_TITLE     => "لقد تم تقييد حسابك",
    NotificationMessages::BAN_BODY      => "حسابك محظور من استخدام بعض ميزات التطبيق حتى تاريخ: :bannedUntil بسبب: :reason",
    NotificationMessages::UNBAN_TITLE   => "لقد تم الغاء تقييد حسابك",
    NotificationMessages::UNBAN_BODY    => "بإمكانك الآن الاستفادة من جميع ميزات التطبيق ,لقد تم الغاء القيود على حسابك",

    //Customer Service Card
    NotificationMessages::CUSTOMER_SERVICE_CARD_DELETE_TITLE    => "لقد تم حذف بطاقة خدمة العملاء خاصتك",
    NotificationMessages::CUSTOMER_SERVICE_CARD_DELETE_BODY     => "تم حذف بطاقة خدمة العملاء خاصتك بالعنوان التالي: ':name'",
    NotificationMessages::CUSTOMER_SERVICE_CARD_CLOSE_TITLE     => "لقد تم إغلاق خدمة العملاء خاصتك",
    NotificationMessages::CUSTOMER_SERVICE_CARD_CLOSE_BODY      => "تم إغلاق بطاقة خدمة العملاء خاصتك بالعنوان التالي: ':name'",

    // Auth Messages
    NotificationMessages::REGISTER_TITLE       => "تسجيل حساب جديد",
    NotificationMessages::REGISTER_BODY        => "مرحبا بك في كارغو اكس",

    NotificationMessages::WELCOME_BACK_TITLE   => "تسجيل دخول",
    NotificationMessages::WELCOME_BACK_BODY    => "أهلاً بعودتك، :name! ",

    NotificationMessages::DEVICE_LOGIN_TITLE   => "دخول من جهاز جديد",
    NotificationMessages::DEVICE_LOGIN_BODY    => "تم تسجيل دخول جديد إلى حسابك",

    //Trip Messages
    NotificationMessages::TRIP_STATUS_CHANGED_TITLE   => "تحديث حالة الرحلة",
    NotificationMessages::TRIP_STATUS_CHANGED_BODY    => "تم تحديث حالة رحلتك إلى ':status'، الرجاء التحقق من تفاصيل الرحلة لمزيد من المعلومات",

    //Clearence Transaction Messages
    NotificationMessages::CLEARENCE_TRANSACTION_CHANGED_TITLE   => "تحديث حالة معاملة التخليص",
    NotificationMessages::CLEARENCE_TRANSACTION_CHANGED_BODY    => "تم تحديث حالة معاملة التخليص الخاصة بك إلى ':status'، الرجاء التحقق من تفاصيل المعاملة لمزيد من المعلومات",

    //Order Messages
    NotificationMessages::ORDER_STATUS_CHANGED_TITLE   => "تحديث حالة الطلب",
    NotificationMessages::ORDER_STATUS_CHANGED_BODY    => "تم تحديث حالة طلبك إلى ':status'، الرجاء التحقق من تفاصيل الطلب لمزيد من المعلومات",

    NotificationMessages::NEW_ORDER_TITLE   => "طلب جديد",
    NotificationMessages::NEW_ORDER_BODY    => "لديك طلب جديد، الرجاء التحقق من تفاصيل الطلب لمزيد من المعلومات",

    //Chat Messages
    NotificationMessages::CHAT_MESSAGE_TITLE   => "رسالة جديدة",
    NotificationMessages::CHAT_MESSAGE_BODY    => "لديك رسالة جديدة",

    //Wallet Messages
    NotificationMessages::WALLET_TRANSACTION_TITLE   => "تحديث في محفظتك",
    NotificationMessages::WALLET_TRANSACTION_BODY    => "تم إجراء معاملة جديدة في محفظتك، الرجاء التحقق من تفاصيل المعاملة لمزيد من المعلومات",

    //Offer Messages
    NotificationMessages::NEW_OFFER_TITLE   => "عرض جديد",
    NotificationMessages::NEW_OFFER_BODY    => "لديك عرض جديد، الرجاء التحقق من تفاصيل العرض لمزيد من المعلومات",

    NotificationMessages::OFFER_STATUS_CHANGED_TITLE   => "تحديث حالة العرض",
    NotificationMessages::OFFER_STATUS_CHANGED_BODY    => "تم تحديث حالة عرضك إلى ':status'، الرجاء التحقق من تفاصيل العرض لمزيد من المعلومات",
];
