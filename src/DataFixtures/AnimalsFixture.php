<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Animal;
use App\Entity\Country;
use Faker\Factory;

class AnimalsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         $faker = Factory::create();

                for ($i = 0; $i < 10; $i++) {
                    $animal = new Animal();
                    $animal->setNom($faker->name);
                    $animal->setTailleMoyenne($faker->randomFloat(2, 0.1, 10.0));
                    $animal->setDureeVieMoyenne($faker->numberBetween(1, 20));
                    $animal->setArtMartial($faker->randomElement(['Karaté', 'Judo', 'Taekwondo', 'Kung Fu', 'Dance Classique']));
                    $animal->setNumeroTelephone($faker->phoneNumber);

                    // Récupérer un pays aléatoire
                    $country = $manager->getRepository(Country::class)->findOneBy([], ['id' => 'ASC']);
                    $animal->setCountry($country);

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
