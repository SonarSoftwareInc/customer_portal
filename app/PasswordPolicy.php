<?php

namespace App;

use ZxcvbnPhp\Zxcvbn;

class PasswordPolicy
{
    private $zxcvbn;

    public function __construct()
    {
        $this->zxcvbn = new Zxcvbn();
    }

    /**
     * Validates the password based on Customer Portal settings.
     * @param String $password Password to validate
     * @return bool True if validation passed; otherwise, false.
     */
    public function isPasswordValid(String $password): bool
    {
        //The settings page was built with 1-5 instead of 0-4, fixing this here instead of making a migration.
        $minimumPasswordStrength = SystemSetting::first()->password_strength_required - 1;
        $zxcvbnResult = $this->zxcvbn->passwordStrength(substr($password, 0, 100));
        return ($zxcvbnResult['score'] >= $minimumPasswordStrength);
    }
}
