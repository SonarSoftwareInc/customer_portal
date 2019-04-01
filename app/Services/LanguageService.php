<?php

namespace App\Services;

use App\UsernameLanguage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class LanguageService
{
    private $language = null;
    private $foundOverride = false;
    public function __construct()
    {
        $language = Lang::getLocale();
        $availableLanguages = getAvailableLanguages();
        $user = get_user();
        if ($user)
        {
            $usernameLanguage = UsernameLanguage::where("username","=",$user->username)->first();
            if ($usernameLanguage && in_array($usernameLanguage->language,array_keys($availableLanguages)))
            {
                $this->foundOverride = true;
                $language = $usernameLanguage->language;
            }
        }
        $this->language = $language;
    }

    /**
     * @param null $request - Pass in the request to optionally load from the cookie
     * @return string
     */
    public function getUserLanguage($request = null)
    {
        if ($request && $request->cookie('language') && $this->foundOverride === false)
        {
            return $request->cookie('language');
        }
        return $this->language;
    }
}