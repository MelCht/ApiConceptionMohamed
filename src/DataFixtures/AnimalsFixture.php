<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Animal;
use App\Entity\Country;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Repository\CountryRepository;

class AnimalsFixture extends Fixture implements DependentFixtureInterface
{
    private $countryRepository;
    public function __construct(CountryRepository $countryRepository) {
        $this->countryRepository = $countryRepository;
    }

    public function load(ObjectManager $manager): void
    {
         $faker = Factory::create();

         $countries = $this->countryRepository->findAll();
         $randomCountryIndex = array_rand($countries);
         $randomCountry = $countries[$randomCountryIndex];

         for ($i = 0; $i < 10; $i++) {
             $animal = new Animal();
             $animal->setNom($faker->name);
             $animal->setTailleMoyenne($faker->randomFloat(2, 0.1, 10.0));
             $animal->setDureeVieMoyenne($faker->numberBetween(1, 20));
             $animal->setArtMartial($faker->randomElement(['Karaté', 'Judo', 'Taekwondo', 'Kung Fu', 'Dance Classique']));
             $animal->setNumeroTelephone($faker->phoneNumber);

             // Associer le pays aléatoire
             $animal->setCountry($randomCountry);

             $manager->persist($animal);
         }

        $manager->flush();
    }

    public function getDependencies()
        {
            return [
                CountryFixture::class,
            ];
        }
}
