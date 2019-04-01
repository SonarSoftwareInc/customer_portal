<?php
return [
    'createAccount' => ':companyName customer portal account creation',
    'passwordReset' => ':companyName password reset',
    'greeting' => 'Hello,',
    'accountCreateBody' => "Someone requested that an account be created at <a href=':portal_url'>:portal_url</a> with this email address. If this was you, please use the following link to create your account: <a href=':creation_link'>:creation_link</a>. This link will be valid for 24 hours.",
    'passwordResetBody' => 'Someone requested a password reset for your account with the username :username for <a href=":portal_url">:portal_url</a>. If this was you, please use the following link to reset your password: <a href=":reset_link">:reset_link</a>. This link will be valid for 24 hours.',
    'deleteIfNotYou' => 'If this was not you, please delete this email. Thanks!',
];