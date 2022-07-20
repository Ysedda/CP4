<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Trip;
use App\Repository\TripRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TripFixtures extends Fixture implements DependentFixtureInterface
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
        for ($i = 0; $i < 30; $i++) {
            $trip = new Trip();
            $trip->setSpots(rand(1, self::MAX_SEATS));
            $trip->setStartLatitude(rand(self::LATITUDE[0], self::LATITUDE[1]));
            $trip->setStartLongitude(rand(self::LONGITUDE[0], self::LONGITUDE[1]));
            $trip->setDate($faker->dateTimeBetween('-1 week', '+1 week'));
            $trip->setMeetingPoint($faker->sentence(3));
            $tripDriver = rand(0, 4);
            $trip->setDriver($this->getReference('user' . $tripDriver));
            $tripPassenger1 = rand(0, 4);
            $tripPassenger2 = rand(0, 4);
            $tripPassenger3 = rand(0, 4);
            $takenSeats = 0;
            if ($tripDriver !== $tripPassenger1) {
                $trip->addPassenger($this->getReference('user' . $tripPassenger1));
                $takenSeats ++;
            }
            if ($tripDriver !== $tripPassenger2) {
                $trip->addPassenger($this->getReference('user' . $tripPassenger2));
                $takenSeats ++;
            }
            if ($tripDriver !== $tripPassenger3) {
                $trip->addPassenger($this->getReference('user' . $tripPassenger3));
                $takenSeats ++;
            }
            if ($trip->getSpots() >= 3) {
                $trip->setSpots($trip->getSpots() - $takenSeats);
            }
            $manager->persist($trip);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
