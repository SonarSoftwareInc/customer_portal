<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class QCoreService
{
    protected $qcore_uri;
    protected $qcore_data;

    public function __construct()
    {
        $qcore_username = config('services.qcore.username');
        $qcore_password = config('services.qcore.password');

        $this->qcore_uri = config('services.qcore.qcore_uri');
        $this->qcore_data = [
            'username' => $qcore_username,
            'password' => $qcore_password,
        ];
    }

    public function getSonarAccountInfo($accountId)
    {
        try {
            $qcore_response = Http::timeout(10)->post($this->qcore_uri . '/api/v1/api-token-auth/', $this->qcore_data);
            
            if ($qcore_response->successful()) {
                $response_data = $qcore_response->json();
                $token = $response_data['token'];

                $response2 = Http::withHeaders([
                    'Authorization' => 'Token ' . $token,
                    'Accept' => 'application/json',
                ])->timeout(10)->get($this->qcore_uri . '/api/v1/qportal/sonar-account-info/' . $accountId . '/');

                if ($response2->successful()) {
                    return $response2->json();
                }
            }
        } catch (Exception $e) {
            return null;
        }

        return null;
    }
}

