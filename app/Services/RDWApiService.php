<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RDWApiService
{
    protected string $baseUrl = 'https://opendata.rdw.nl/resource/m9d7-ebf2.json';
    protected string $appToken;

    public function __construct()
    {
        $this->appToken = env('RDW_APP_TOKEN', '');
        if (empty($this->appToken)) {
            Log::error('RDW App Token is not set in environment variables');
            throw new \Exception('RDW App Token is missing in .env file');
        }
        Log::debug('RDW App Token loaded: ' . $this->appToken);
    }

    public function getVehicleByLicensePlate(string $kenteken): ?array
    {
        $kenteken = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $kenteken));
        Log::info("Processing license plate: $kenteken");

        if (!preg_match('/^[A-Z0-9]{6}$/', $kenteken)) {
            Log::warning("Invalid license plate format: $kenteken");
            return ['error' => 'Invalid license plate format. Must be 6 alphanumeric characters (e.g., 1XXD48).'];
        }

        try {
            Log::info("Sending request to RDW API for $kenteken");
            $response = Http::withOptions(['timeout' => 10])->get($this->baseUrl, [
                'kenteken' => $kenteken,
                '$$app_token' => $this->appToken,
            ]);

            Log::debug("RDW API request URL: " . $this->baseUrl . '?' . http_build_query([
                'kenteken' => $kenteken,
                '$$app_token' => $this->appToken,
            ]));

            if ($response->successful()) {
                $data = $response->json();
                Log::debug("RDW API response: " . json_encode($data));
                if (!empty($data) && is_array($data)) {
                    return $data[0];
                }
                Log::info("No data found for license plate: $kenteken");
                return null;
            }

            if ($response->status() === 403) {
                Log::error("RDW API 403 Forbidden for $kenteken: " . $response->body());
                return ['error' => 'Access denied. Please verify your RDW app token. Response: ' . $response->body()];
            }

            Log::error("RDW API request failed for $kenteken: Status " . $response->status() . " - Body: " . $response->body());
            return ['error' => 'Error retrieving data for license plate ' . $kenteken . ' (Status: ' . $response->status() . ')'];
        } catch (\Exception $e) {
            Log::error("RDW API exception for $kenteken: " . $e->getMessage() . ' | Stack trace: ' . $e->getTraceAsString());
            return ['error' => 'Failed to retrieve data: ' . $e->getMessage()];
        }
    }
}