<?php

namespace App\Service;

use App\Entity\Trip;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Route
{
    public const URL = 'http://router.project-osrm.org/route/v1/driving/';
    public const AFTEC_COORDINATES = ['47.918404', '1.924932'];

    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    public function getGeoJson(Trip $trip): ?array
    {
        
        $response = $this->client->request(
            'GET',
            self::URL . $trip->getStartLongitude() . ',' . $trip->getStartLatitude() .';' . self::AFTEC_COORDINATES[1] . ',' . self::AFTEC_COORDINATES[0] ,
            [
                'query' => [
                    'overview' => 'full',
                    'geometries' => 'geojson'
                ]
            ]
        );
        
        $data = $response->toArray();
        return $data;
    }
}
