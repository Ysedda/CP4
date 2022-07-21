<?php

namespace App\Service;

use App\Entity\Trip;
use App\Entity\User;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Locator
{
    public const URL = 'https://api-adresse.data.gouv.fr/search/';

    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    public function setCoordinates(Trip $trip): void
    {
        $response = $this->client->request(
            'GET',
            self::URL,
            [
                'query' => [
                    'q' => $trip->getMeetingPoint(),
                ]
            ]
        );

        if ($response->getStatusCode() === 200) {
            $data = $response->toArray();
            if (isset($data['features'][0])) {
                $longitude = $data['features'][0]['geometry']['coordinates'][0];
                $latitude = $data['features'][0]['geometry']['coordinates'][1];
                $trip->setStartLatitude($latitude)->setStartLongitude($longitude);
            } else {
                throw new Exception('Adresse non trouvée');
            }
        } else {
            throw new Exception('Une erreur est survenue lors de la récupération de l\'adresse');
        }
    }

    public function setAddressCoordinates(User $user): void
    {
        $response = $this->client->request(
            'GET',
            self::URL,
            [
                'query' => [
                    'q' => $user->getAddress(),
                ]
            ]
        );

        if ($response->getStatusCode() === 200) {
            $data = $response->toArray();
            if (isset($data['features'][0])) {
                $longitude = $data['features'][0]['geometry']['coordinates'][0];
                $latitude = $data['features'][0]['geometry']['coordinates'][1];
                $user->setAddressLatitude($latitude)->setAddressLongitude($longitude);
            } else {
                throw new Exception('Adresse non trouvée');
            }
        } else {
            throw new Exception('Une erreur est survenue lors de la récupération de l\'adresse');
        }
    }
}
