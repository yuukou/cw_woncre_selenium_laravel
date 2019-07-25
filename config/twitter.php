<?php
/**
 * @package
 * @copyright Copyright (C) Logical-Studio Co.,Ltd.
 * @since 2019-07-07
 */

return [
    'api_key' => env('TWITTER_CLIENT_KEY', ''),
    'secret_key' => env('TWITTER_CLIENT_SECRET', ''),
    'access_token' => env('TWITTER_CLIENT_ID_ACCESS_TOKEN', ''),
    'access_token_secret' => env('TWITTER_CLIENT_ID_ACCESS_TOKEN_SECRET', ''),
    'call_back_url' => env('TWITTER_CALLBACK_URL', ''),
];
