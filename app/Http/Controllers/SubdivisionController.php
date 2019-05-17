<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Response;

class SubdivisionController extends Controller
{
    /**
     * Get subdivisions for a country,
     * for user on app settings page
     * @param Request $request
     * @param $country
     * @return mixed
     */
    public function authenticate(Request $request, $country)
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
    }

    /**
     * Get subdivisions for a country
     * @param $country
     * @return mixed
     */
    public function show($country)
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
