<?php

return [
    'send_otp_url'    => env('MSG91_SEND_OTP_URL', 'https://api.msg91.com/api/v5/otp'),
    'verify_url'      => env('MSG91_VERIFY_URL', 'https://api.msg91.com/api/v5/otp/verify'),
    'auth_key'        => env('MSG91_AUTH_KEY', '368708AmFg9wMEc361fd6826P1'),
    'template_id'     => env('MSG91_TEMPLATE_ID', '61fd64d6a2344638563c3a99'),
    'template_text' => env('MSG91_TEMPLATE_TEXT', 'Your Lifeapp Verification OTP code is {token}. Please DO NOT share this OTP with anyone.'),
    'resend_url'      => env('MSG91_RESEND_URL', 'https://api.msg91.com/api/v5/otp/retry'),
];
