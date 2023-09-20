<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SubdivisionController extends Controller
{
    /**
     * Get subdivisions for a country,
     * for user on app settings page
     */
    public function authenticate(Request $request, $country): JsonResponse
    {
        if ($request->session()->get('settings_authenticated') === 1) {
            try {
                $subdivisions = subdivisions($country);
            } catch (Exception $e) {
                return Response::json([
                    'error' => $e->getMessage(),
                ], 422);
            }

            return Response::json([
                'subdivisions' => $subdivisions,
            ], 200);
        }

        return Response::json([
            'error' => __('errors.invalidSettingsKey'),
        ], 422);
    }

    /**
     * Get subdivisions for a country
     */
    public function show($country): JsonResponse
    {
        try {
            $subdivisions = subdivisions($country);
        } catch (Exception $e) {
            return Response::json([
                'error' => $e->getMessage(),
            ], 422);
        }

        return Response::json([
            'subdivisions' => $subdivisions,
        ], 200);
    }
}
