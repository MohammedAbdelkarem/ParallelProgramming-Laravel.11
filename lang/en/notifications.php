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
    NotificationTypes::AUTH->value           => "Authentication Notifications",
    NotificationTypes::TRIP->value           => "Trip Notifications",
    NotificationTypes::CHAT->value           => "Chat Notifications",
    NotificationTypes::COMPLAINT->value      => "Complaint Notifications",
    NotificationTypes::WALLET->value         => "Wallet Notifications",
    NotificationTypes::OTHER->value          => "Other Notifications",
    NotificationTypes::CLEARENCE_TRANSACTION->value => "Clearence Transaction Notifications",
    NotificationTypes::ORDER->value          => "Order Notifications",
    //
    "Suspend" => "Suspend",
    "Unblock" => "Unblock",

    //Messages
    //Account
    NotificationMessages::LOGIN_TITLE   => "New Login attepmt",
    NotificationMessages::LOGIN_BODY    => "A new login attempt was made from a :device device from :location",
    NotificationMessages::BAN_TITLE     => "Your account has been suspended",
    NotificationMessages::BAN_BODY      => "Your account has been suspended until :bannedUntil due to: :reason",
    NotificationMessages::UNBAN_TITLE   => "Your account has been unblocked",
    NotificationMessages::UNBAN_BODY    => "You can now use all application features, your account has been unblocked",

    //Customer Service Card
    NotificationMessages::CUSTOMER_SERVICE_CARD_DELETE_TITLE    => "Your customer service card has been delete",
    NotificationMessages::CUSTOMER_SERVICE_CARD_DELETE_BODY     => "Your customer service card with the title: ':name' has been deleted",
    NotificationMessages::CUSTOMER_SERVICE_CARD_CLOSE_TITLE     => "Your customer service card has been closed",
    NotificationMessages::CUSTOMER_SERVICE_CARD_CLOSE_BODY      => "Your customer service card with the title: ':name' has been closed",

    // Auth Messages
    NotificationMessages::REGISTER_TITLE => "New Account Registration",
    NotificationMessages::REGISTER_BODY  => "Welcome to CargoX",

    NotificationMessages::WELCOME_BACK_TITLE => "Login",
    NotificationMessages::WELCOME_BACK_BODY  => "Welcome back, :name!",

    NotificationMessages::DEVICE_LOGIN_TITLE => "New Device Login",
    NotificationMessages::DEVICE_LOGIN_BODY  => "A new login has been detected on your account",


    // Trip Messages
    NotificationMessages::TRIP_STATUS_CHANGED_TITLE => "Trip Status Update",
    NotificationMessages::TRIP_STATUS_CHANGED_BODY  => "Your trip status has been updated to ':status'. Please check the trip details for more information.",

    // Clearance Transaction Messages
    NotificationMessages::CLEARENCE_TRANSACTION_CHANGED_TITLE => "Clearance Transaction Status Update",
    NotificationMessages::CLEARENCE_TRANSACTION_CHANGED_BODY  => "Your clearance transaction status has been updated to ':status'. Please check the transaction details for more information.",

    // Order Messages
    NotificationMessages::ORDER_STATUS_CHANGED_TITLE => "Order Status Update",
    NotificationMessages::ORDER_STATUS_CHANGED_BODY  => "Your order status has been updated to ':status'. Please check the order details for more information.",

    NotificationMessages::NEW_ORDER_TITLE => "New Order",
    NotificationMessages::NEW_ORDER_BODY  => "You have a new order. Please check the order details for more information.",
    // Chat Messages
    NotificationMessages::CHAT_MESSAGE_TITLE => "New Message",
    NotificationMessages::CHAT_MESSAGE_BODY  => "You have a new message.",

    // Wallet Messages
    NotificationMessages::WALLET_TRANSACTION_TITLE => "Wallet Update",
    NotificationMessages::WALLET_TRANSACTION_BODY  => "A new transaction has been made in your wallet. Please check the transaction details for more information.",

    // Offer Messages
    NotificationMessages::NEW_OFFER_TITLE => "New Offer",
    NotificationMessages::NEW_OFFER_BODY  => "You have a new offer. Please check the offer details for more information.",

    NotificationMessages::OFFER_STATUS_CHANGED_TITLE => "Offer Status Update",
    NotificationMessages::OFFER_STATUS_CHANGED_BODY  => "Your offer status has been updated to ':status'. Please check the offer details for more information."
];
