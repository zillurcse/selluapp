<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use App\Services\Courier\PathaoService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings.view', only: ['index', 'show']),
            new Middleware('permission:settings.manage', only: ['store', 'update', 'destroy']),
        ];
    }

    /**
     * Display a listing of the cities.
     */
    public function index(Request $request)
    {
        $query = City::with('state.country');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('state_id')) {
            $query->where('state_id', $request->input('state_id'));
        }

        if ($request->has('status')) {
            $query->where('status', (bool) $request->input('status'));
        }

        $perPage = $request->input('per_page', 10);
        $cities = $query->latest()->paginate($perPage);

        return response()->json($cities);
    }

    /**
     * Store a newly created city in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'cost' => 'nullable|numeric',
            'status' => 'boolean',
            'pathao_city_id' => 'nullable|string|max:255',
            'pathao_zone_id' => 'nullable|string|max:255',
            'pathao_area_id' => 'nullable|string|max:255'
        ]);

        $city = City::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'City created successfully',
            'data' => $city
        ], 201);
    }

    /**
     * Display the specified city.
     */
    public function show(City $city)
    {
        return response()->json([
            'status' => 'success',
            'data' => $city->load('state.country')
        ]);
    }

    /**
     * Update the specified city in storage.
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'state_id' => 'sometimes|required|exists:states,id',
            'cost' => 'nullable|numeric',
            'status' => 'sometimes|boolean',
            'pathao_city_id' => 'nullable|string|max:255',
            'pathao_zone_id' => 'nullable|string|max:255',
            'pathao_area_id' => 'nullable|string|max:255'
        ]);

        $city->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'City updated successfully',
            'data' => $city
        ]);
    }

    /**
     * Remove the specified city from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'City deleted successfully'
        ]);
    }

    /**
     * Fetch states for the city creation/management UI.
     */
    public function get_states()
    {
        $states = State::where('status', true)->with('country')->get();
        return response()->json([
            'status' => 'success',
            'data' => $states
        ]);
    }

    public function syncPathaoLocations()
    {
        set_time_limit(0); 
        ini_set('memory_limit', '1024M');

        $pathao = new PathaoService();
        $response = $pathao->getCities();
        
        if (isset($response['data']['data'])) {
            $pathaoCities = $response['data']['data'];
            
            // Map Pathao City to local State ID
            $states = State::all();
            $stateMap = [];
            foreach ($states as $st) {
                $name = strtolower(str_replace([' ', '-', '.'], '', $st->name));
                $stateMap[$name] = $st->id;
            }

            $syncedCount = 0;

            foreach ($pathaoCities as $pCity) {
                $pCityId = (int)$pCity['city_id'];
                $pCityName = trim($pCity['city_name']);
                
                $matchName = strtolower(str_replace([' ', '-', '.'], '', $pCityName));
                // Handle common differences
                if ($matchName == 'jashore') $matchName = 'jessor';
                if ($matchName == 'chittagong') $matchName = 'chattagam';
                if ($matchName == 'cumilla') $matchName = 'komilla';
                if ($matchName == 'gaibandha') $matchName = 'gaybanda';
                if ($matchName == 'bbaria') $matchName = 'brahmanbariya';
                if ($matchName == 'bagerhat') $matchName = 'bagarhat';

                $stateId = $stateMap[$matchName] ?? 1; 

                $zonesRes = $pathao->getZones($pCityId);
                if (!isset($zonesRes['data']['data'])) continue;

                foreach ($zonesRes['data']['data'] as $pZone) {
                    $pZoneId = (int)$pZone['zone_id'];
                    $pZoneName = trim($pZone['zone_name']);
                    
                    $areasRes = $pathao->getAreas($pZoneId);
                    $areas = isset($areasRes['data']['data']) ? $areasRes['data']['data'] : [];

                    if (empty($areas)) {
                        $this->upsertPathaoCityRecord($pCityName, $pZoneName, null, $pCityId, $pZoneId, null, $stateId);
                        $syncedCount++;
                    } else {
                        foreach ($areas as $pArea) {
                            $pAreaId = (int)$pArea['area_id'];
                            $pAreaName = trim($pArea['area_name']);
                            $this->upsertPathaoCityRecord($pCityName, $pZoneName, $pAreaName, $pCityId, $pZoneId, $pAreaId, $stateId);
                            $syncedCount++;
                        }
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Sync complete. $syncedCount Pathao locations synced."
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => translate('Failed to fetch cities from Pathao.')
        ], 400);
    }

    private function upsertPathaoCityRecord($cityName, $zoneName, $areaName, $pCityId, $pZoneId, $pAreaId, $stateId)
    {
        $query = City::where('pathao_city_id', $pCityId)
                     ->where('pathao_zone_id', $pZoneId);
        
        if ($pAreaId) {
            $query->where('pathao_area_id', $pAreaId);
        } else {
            $query->whereNull('pathao_area_id');
        }

        $city = $query->first();

        if (!$city) {
            $city = new City();
            $city->cost = 0;
            $city->status = 1;
        }

        if ($areaName) {
            $city->name = "{$areaName}, {$zoneName}, {$cityName}";
        } else {
            $city->name = "{$zoneName}, {$cityName}";
        }

        $city->pathao_city_id = $pCityId;
        $city->pathao_zone_id = $pZoneId;
        $city->pathao_area_id = $pAreaId;
        $city->state_id = $stateId;
        $city->save();
    }
}
