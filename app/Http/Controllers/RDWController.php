<?php

namespace App\Http\Controllers;

use App\Services\RDWApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RDWController extends Controller
{
    protected RDWApiService $rdwService;

    public function __construct(RDWApiService $rdwService)
    {
        $this->rdwService = $rdwService;
        Log::info('RDWController instantiated');
    }

    public function search(Request $request)
    {
        try {
            $kenteken = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $request->input('kenteken')));
            Log::info("Received search request for license plate: $kenteken");

            if (empty($kenteken)) {
                Log::warning('No license plate provided in search request');
                return response()->json(['error' => 'Geen kenteken opgegeven'], 400);
            }

            $data = $this->rdwService->getVehicleByLicensePlate($kenteken);
            Log::debug("RDW service returned data: " . json_encode($data));

            if (isset($data['error'])) {
                Log::warning("Error fetching RDW data: " . $data['error']);
                return response()->json(['error' => $data['error']], 400);
            }

            if (!$data) {
                Log::info("No RDW data found for license plate: $kenteken");
                return response()->json(['error' => 'Geen gegevens gevonden voor dit kenteken'], 404);
            }

            Log::info("Successfully retrieved RDW data for $kenteken");
            return response()->json([
                'merk' => $data['merk'] ?? '',
                'handelsbenaming' => $data['handelsbenaming'] ?? '',
                'kleur' => $data['eerste_kleur'] ?? '',
                'datum_eerste_toelating' => $data['datum_eerste_toelating'] ?? '',
                'aantal_zitplaatsen' => $data['aantal_zitplaatsen'] ?? '',
                'aantal_deuren' => $data['aantal_deuren'] ?? '',
                'massa_rijklaar' => $data['massa_rijklaar'] ?? '',
            ]);
        } catch (\Exception $e) {
            Log::error('RDW API error in search: ' . $e->getMessage() . ' | Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Er ging iets mis bij het ophalen van de kentekengegevens: ' . $e->getMessage()], 500);
        }
    }

    public function showRdwData(Request $request)
    {
        try {
            $licensePlate = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $request->input('kenteken', '1XXD48')));
            Log::info("Fetching RDW data for license plate in showRdwData: $licensePlate");
            $carData = $this->rdwService->getVehicleByLicensePlate($licensePlate);

            if (isset($carData['error'])) {
                Log::warning("Error in showRdwData: " . $carData['error']);
                return view('cars.rdw', compact('carData', 'licensePlate'))->with('error', $carData['error']);
            }

            Log::info('Rendering cars.rdw view with data');
            return view('cars.rdw', compact('carData', 'licensePlate'));
        } catch (\Exception $e) {
            Log::error('RDW API error in showRdwData: ' . $e->getMessage() . ' | Stack trace: ' . $e->getTraceAsString());
            $carData = null;
            return view('cars.rdw', compact('carData', 'licensePlate'))->with('error', 'Er ging iets mis bij het ophalen van de kentekengegevens: ' . $e->getMessage());
        }
    }
}