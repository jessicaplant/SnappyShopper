<?php

namespace App\Services;

use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Support\Collection;

class StoreManagementService implements StoreManagementServiceInterface
{

    /**
     * @inheritDoc
     *
     * @param array $parameters
     * @return Store
     */
    public function createOne(array $parameters): Store
    {
        return Store::create($parameters)->fresh();
    }

    /**
     * @inheritDoc
     *
     * @param array $parameters
     * @return Collection
     */
    public function findMany(array $parameters): Collection
    {
        return collect([]);
    }

    /**
     * Finds all postcodes within a specified radius of a given point.
     *
     * @param float $latitude Latitude of the central point
     * @param float $longitude Longitude of the central point
     * @param float $radius Radius in kilometers
     *
     * @return array An array of postcodes within the specified radius
     */
    private function findPostcodesWithinRadius($latitude, $longitude, $radius)
    {
        $pointsWithinRadius = [];
        foreach (Postcode::all() as $postcode) {
            $distance = $this->calculateHaversineDistance($latitude, $longitude, $postcode->lat, $postcode->long);
            if ($distance <= $radius) {
                $pointsWithinRadius[] = $postcode;
            }
        }
        return $pointsWithinRadius;
    }

    /**
     * @param $latitudeFrom
     * @param $longitudeFrom
     * @param $latitudeTo
     * @param $longitudeTo
     * @param int $earthRadius
     * @return float|int
     */
    private function calculateHaversineDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, int $earthRadius = 6371)
    {
        // Convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

}
