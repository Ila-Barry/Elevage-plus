<?php
// config/webpush.php

return [
    /*
    |--------------------------------------------------------------------------
    | VAPID Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'authentification des notifications push.
    | Les clés VAPID sont générées avec `npx web-push generate-vapid-keys`
    |
    */
    'vapid' => [
        'public_key' => env('WEBPUSH_VAPID_PUBLIC_KEY'),
        'private_key' => env('WEBPUSH_VAPID_PRIVATE_KEY'),
        'subject' => env('WEBPUSH_VAPID_SUBJECT'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Délai d'expiration des notifications
    |--------------------------------------------------------------------------
    */
    'ttl' => env('WEBPUSH_TTL', 24 * 60 * 60), // 24 heures par défaut
    
    /*
    |--------------------------------------------------------------------------
    | Modèle de l'abonnement
    |--------------------------------------------------------------------------
    */
    'model' => NotificationChannels\WebPush\WebPushSubscription::class,
];
