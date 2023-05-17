namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Country;
use Faker\Factory;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $countriesData = [
               ['nom' => 'France', 'codeISO' => 'FR'],
               ['nom' => 'Allemagne', 'codeISO' => 'DE'],
               ['nom' => 'Espagne', 'codeISO' => 'ES'],
               ['nom' => 'Italie', 'codeISO' => 'IT'],
               ['nom' => 'Royaume-Uni', 'codeISO' => 'GB'],
               ['nom' => 'États-Unis', 'codeISO' => 'US'],
               ['nom' => 'Canada', 'codeISO' => 'CA'],
               ['nom' => 'Brésil', 'codeISO' => 'BR'],
               ['nom' => 'Russie', 'codeISO' => 'RU'],
               ['nom' => 'Chine', 'codeISO' => 'CN'],
               ['nom' => 'Japon', 'codeISO' => 'JP'],
               ['nom' => 'Australie', 'codeISO' => 'AU'],
               ['nom' => 'Inde', 'codeISO' => 'IN'],
               ['nom' => 'Mexique', 'codeISO' => 'MX'],
               ['nom' => 'Argentine', 'codeISO' => 'AR'],
               ['nom' => 'Afrique du Sud', 'codeISO' => 'ZA'],
        ];

        foreach ($countriesData as $countryData) {
            $country = new Country();
            $country->setNom($countryData['nom']);
            $country->setCodeISO($countryData['codeISO']);

            $manager->persist($country);
        }

        for ($i = 0; $i < 10; $i++) {
            $country = new Country();
            $country->setNom($faker->country);
            $country->setCodeISO($faker->countryCode);

            $manager->persist($country);
        }

        $manager->flush();
    }
}
