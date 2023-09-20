<?php

namespace App\Http\Controllers;

use App\Http\Requests\LanguageUpdateRequest;
use App\UsernameLanguage;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    public function update(LanguageUpdateRequest $request): JsonResponse
    {
        $language = $request->input('language');
        if (get_user()) {
            $usernameLanguage = UsernameLanguage::firstOrNew([
                'username' => get_user()->username,
            ]);
            $usernameLanguage->language = $language;
            $usernameLanguage->save();
        }

        return response()->json([
            'success' => true,
        ])->cookie('language', $language, 31536000);
    }
}
