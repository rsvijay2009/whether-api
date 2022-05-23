<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{City, WhetherReport};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Constants\ApiResponseCodes;
use Illuminate\Support\Facades\Log;

class WhetherController extends Controller
{
    public function __construct() {
        $this->openWhetherApiKey = env('OPEN_WHETHER_API_KEY');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $cityName)
    {
        try {
            $city = City::where('name', $cityName)
            ->select('id')
            ->first();
            $cityId = $city->id ?? null;
            
            $whetherData = WhetherReport::where('city_name', $cityName)->first();
            if($whetherData) {
                $cityName = $whetherData->city_name;
                $latitude = $whetherData->lat;
                $longitude = $whetherData->lon;
                $whetherDescription = $whetherData->weather_description;
                $whetherIcon = $whetherData->weather_icon;
                $temperature = $whetherData->temperature;
                $minTemperature = $whetherData->min_temperature;
                $maxTemperature = $whetherData->max_temperature;
                $pressure = $whetherData->pressure;
                $humidity = $whetherData->humidity;
                $seaLevel = $whetherData->sea_level;
                $grndLevel = $whetherData->grnd_level;
                $country = $whetherData->country;
            } else {
                $response = Http::get("https://api.openweathermap.org/data/2.5/weather?q=$cityName&appid=$this->openWhetherApiKey");
                $responseData = $response->json();

                if(!empty($responseData)) {
                    $id = $responseData['id'] ?? null;
                    $cityName = $responseData['name'] ?? null;
                    $latitude = $responseData['coord']['lat'] ?? null;
                    $longitude = $responseData['coord']['lon'] ?? null;
                    $whetherDescription = $responseData['weather'][0]['description'] ?? null;
                    $whetherIcon = $responseData['weather'][0]['icon'] ?? null;
                    $temperature = $responseData['main']['temp'] ?? null;
                    $minTemperature = $responseData['main']['temp_min'] ?? null;
                    $maxTemperature = $responseData['main']['temp_max'] ?? null;
                    $pressure = $responseData['main']['pressure'] ?? null;
                    $humidity = $responseData['main']['humidity'] ?? null;
                    $seaLevel = $responseData['main']['sea_level'] ?? null;
                    $grndLevel = $responseData['main']['grnd_level'] ?? null;
                    $country = $responseData['sys']['country'] ?? null;

                    if(empty($cityId)) {
                        $city = new City();
                        $city->name = $cityName;
                        $city->save();
                        $cityId = $city->id;
                    }
                    $whetherReport = new WhetherReport();
                    $whetherReport->city_id = $cityId;
                    $whetherReport->city_name = $cityName;
                    $whetherReport->lat = $latitude;
                    $whetherReport->lon = $longitude;
                    $whetherReport->whether_description = $whetherDescription;
                    $whetherReport->whether_icon = $whetherIcon;
                    $whetherReport->temperature = $temperature;
                    $whetherReport->min_temperature = $minTemperature;
                    $whetherReport->max_temperature = $maxTemperature;
                    $whetherReport->pressure = $pressure;
                    $whetherReport->humidity = $humidity;
                    $whetherReport->sea_level = $seaLevel;
                    $whetherReport->grnd_level = $grndLevel;
                    $whetherReport->country = $country;
                    $whetherReport->save();
                }                
            }

            return [
                "city_name" => $cityName,
                "coord" => [
                    'lat' => $latitude,
                    'lon' => $longitude,
                ],
                "whether" => [
                    'description' => $whetherDescription,
                    'icon' => $whetherIcon,
                ],
                "main" => [
                    'temp' => $temperature,
                    'min_temp' => $minTemperature,
                    'max_temp' => $maxTemperature,
                    'pressure' => $pressure,
                    'humidity' => $humidity,
                    'sea_level' => $seaLevel,
                    'grnd_level' => $grndLevel,
                    'country' => $country
                ]
            ];
        } catch(\Exception $e) {
            Log::error(get_class($this) . ".php Line no: " . $e->getLine() . " Error msg:" . $e->getMessage());
            return response()->json(['msg' => 'Something is really going wrong'], ApiResponseCodes::BAD_REQUEST);
        }
    }

    /**
     * Show five days whether report of a city
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fiveDaysWhether(Request $request, $cityName) {
        try {
            $response = Http::get("https://api.openweathermap.org/data/2.5/forecast?q=$cityName&appid=$this->openWhetherApiKey");
            $responseData = $response->json();

            return $responseData;
        } catch(\Exception $e) {
            Log::error(get_class($this) . ".php Line no: " . $e->getLine() . " Error msg:" . $e->getMessage());
            return response()->json(['msg' => 'Something is really going wrong'], ApiResponseCodes::BAD_REQUEST);
        }
    }
}