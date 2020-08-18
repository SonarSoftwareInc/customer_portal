<?php
return [
    'createAccount' => ':companyName customer portal account creation',
    'passwordReset' => ':companyName password reset',
    'greeting' => 'Hello,',
    'accountCreateBody' => "Someone requested that an account be created with this email address. If this was you, please use the following link to create your account: <a href=':creation_link'>:creation_link</a>. This link will be valid for 24 hours.",
    'passwordResetBody' => 'Someone requested a username or password recovery for your <u>:isp_name</u> account. Your username is <u><b>:username</b></u>. If you want to reset your password, please proceed to <a href=":reset_link">:reset_link</a> (link is valid for 24 hours).',
    'deleteIfNotYou' => 'If you did not initiate this request, please ignore this email. Thank you.',
];
