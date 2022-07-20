<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Trip;
use App\Repository\TripRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TripFixtures extends Fixture
{
    public const DRIVERS = ['Alain', 'Serge', 'Maxime', 'John', 'Henri'];
    public const LATITUDE = [45, 47];
    public const LONGITUDE = [0, 2];
    public const MAX_SEATS = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 0; $i < 10; $i++) {
            $trip = new Trip();
            $trip->setSpots(rand(0, self::MAX_SEATS));
            $trip->setStartLatitude(rand(self::LATITUDE[0], self::LONGITUDE[1]));
            $trip->setStartLongitude(rand(self::LONGITUDE[0], self::LONGITUDE[1]));
            $trip->setDate($faker->dateTimeBetween('-1 week', '+1 week'));

            $manager->persist($trip);
        }
        $manager->flush();
    }
}
