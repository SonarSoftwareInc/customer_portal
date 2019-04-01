<?php

use App\Services\LanguageService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/**
 * Get the user object from the session
 * @return mixed|null
 */
function get_user()
{
    if (!Session::has("user")) {
        return null;
    }

    return Session::get("user");
}

/**
 * Convert bytes to gigabytes
 * @param $value
 * @return string
 */
function bytes_to_gigabytes($value)
{
    return round($value/1000**4, 2) . "GB";
}

/**
 * Get the configured languages on the system
 * @param string $language
 * @return array
 */
function getAvailableLanguages($language = "en")
{
    $languages = [];
    $dirs = glob(resource_path("lang/*"));
    foreach ($dirs as $dir)
    {
        $boom = explode("/",$dir);
        if (strlen($boom[count($boom)-1]) === 2)
        {
            $languages[$boom[count($boom)-1]] = trans("languages." . $boom[count($boom)-1],[],$language);
        }
    }
    return $languages;
}

/**
 * Translate to the user language
 * @param string $string
 * @param array $variables
 * @param null $request
 * @return \Illuminate\Contracts\Translation\Translator|string
 */
function utrans(string $string, array $variables = [], $request = null)
{
    $languageService = App::make(LanguageService::class);
    $language = $languageService->getUserLanguage($request);
    return trans($string, $variables, $language);
}
