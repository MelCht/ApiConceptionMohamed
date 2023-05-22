<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Optional;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AnimalRepository;

class AnimalsController extends AbstractController
{

        private $animalRepository;

        public function __construct(AnimalRepository $animalRepository) {
            $this->animalRepository = $animalRepository;
        }

    // Récupérer la liste de tous les animaux: (ok)
    #[Route('/animals/all', name: 'app_animals', methods: ['GET'])]
    public function getAllAnimals(): JsonResponse
    {
        $animals = $this->animalRepository->findAll();

        return new JsonResponse($animals);
    }

    // Récupérer un animal par son id: (ok)
    #[Route('/animals/{id}', name: 'app_animal', methods: ['GET'])]
    public function getAnimalById(int $id): JsonResponse
    {
        $animal = $this->getDoctrine()->getRepository(Animal::class)->find($id);

        if (!$animal) {
            return $this->json(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($animal);
    }

    // Récupérer la liste des animaux appartenant à un pays: (ok, mais ils sont tous canadien -> id 7)
    #[Route('/animals/country/{countryId}', name: 'app_animals_by_country', methods: ['GET'])]
    public function getAnimalsByCountry(int $countryId): JsonResponse
    {
         $country = $this->getDoctrine()->getRepository(Country::class)->find($countryId);

         if (!$country) {
            return $this->json(['message' => 'Pays non disponible'], Response::HTTP_NOT_FOUND);
         }

         $animals = $this->getDoctrine()->getRepository(Animal::class)->findBy(['country' => $country]);

         return $this->json($animals);
    }

    // Ajouter un animal: (ok)
     #[Route('/animals/add', name: 'app_animals_add', methods: ['POST'])]
     public function create(Request $request): Response
     {
         $data = json_decode($request->getContent(), true);

         $validator = Validation::createValidator();
         $constraint = new Collection([
             'nom' => [new Type('string'), new Optional()],
             'tailleMoyenne' => [new Type('float'), new Optional()],
             'pays' => new Type('integer'),
             'dureeVieMoyenne' => [new Type('float'), new Optional()],
             'artMartial' => [new Type('string'), new Optional()],
             'numeroTelephone' => [new Type('string'), new Optional()],
         ]);

         $violations = $validator->validate($data, $constraint);

         if (count($violations) > 0) {
             return $this->json(['errors' => $violations], Response::HTTP_BAD_REQUEST);
         }

         $entityManager = $this->getDoctrine()->getManager();

         // Récupérer le pays par son ID
         $pays = $entityManager->getRepository(Country::class)->find($data['pays']);

         if (!$pays) {
             return $this->json(['message' => 'Pays non trouvé'], Response::HTTP_NOT_FOUND);
         }

         // Créer un nouvel animal
         $animal = new Animal();
         $animal->setNom($data['nom']);
         $animal->setTailleMoyenne($data['tailleMoyenne']);
         $animal->setCountry($pays);
         $animal->setDureeVieMoyenne($data['dureeVieMoyenne']);
         $animal->setArtMartial($data['artMartial']);
         $animal->setNumeroTelephone($data['numeroTelephone']);

         // Enregistre l'animal dans la base de données
         $entityManager->persist($animal);
         $entityManager->flush();

         // Renvoie l'animal créé dans la réponse
         return $this->json($animal, Response::HTTP_CREATED);
     }


    // Supprimer un animal par son id: (ok)
    #[Route('/animals/{id}', name: 'app_delete_animal', methods: ['DELETE'])]
    public function deleteAnimal(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $animal = $entityManager->getRepository(Animal::class)->find($id);

        if (!$animal) {
            return $this->json(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($animal);
        $entityManager->flush();

        return $this->json(['message' => 'Animal supprimé']);
    }

    // Mettre à jour un animal: (ok)
   #[Route('/animals/{id}', name: 'app_update_animal', methods: ['PUT'])]
   public function updateAnimal(int $id, Request $request): JsonResponse
   {
       $animal = $this->getDoctrine()->getRepository(Animal::class)->find($id);

       if (!$animal) {
           return $this->json(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
       }

       $data = json_decode($request->getContent(), true);

       // Mets à jour l'animal avec les nouvelles données
       if (isset($data['nom'])) {
           $animal->setNom($data['nom']);
       }

       if (isset($data['tailleMoyenne'])) {
           $animal->setTailleMoyenne($data['tailleMoyenne']);
       }

       if (isset($data['pays'])) {
           $country = null;
           if ($data['pays'] !== null) {
               $country = $this->getDoctrine()->getRepository(Country::class)->find($data['pays']);

               if (!$country) {
                   return $this->json(['message' => 'Pays non trouvé'], Response::HTTP_NOT_FOUND);
               }
           }

           $animal->setCountry($country);
       }

       if (isset($data['dureeVieMoyenne'])) {
           $animal->setDureeVieMoyenne($data['dureeVieMoyenne']);
       }

       if (isset($data['artMartial'])) {
           $animal->setArtMartial($data['artMartial']);
       }

       if (isset($data['numeroTelephone'])) {
           $animal->setNumeroTelephone($data['numeroTelephone']);
       }

       // Enregistre la mise à jour dans la base de données
       $entityManager = $this->getDoctrine()->getManager();
       $entityManager->persist($animal);
       $entityManager->flush();

       return $this->json($animal);
   }


    // Mettre à jour le pays d’un animal: (ok)
    #[Route('/animals/{id}/country/{countryId}', name: 'app_update_animal_country', methods: ['PUT'])]
    public function updateAnimalCountry(int $id, int $countryId): JsonResponse
    {
        $animal = $this->getDoctrine()->getRepository(Animal::class)->find($id);
        $country = $this->getDoctrine()->getRepository(Country::class)->find($countryId);

        if (!$animal) {
            return $this->json(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if (!$country) {
            return $this->json(['message' => 'Pays non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $animal->setCountry($country);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($animal);
        $entityManager->flush();

        return $this->json([
            'message' => 'Pays de l\'animal mis à jour',
            'animal' => $animal,
        ]);
    }

    //Voir les pays
     //#[Route('/countries', name: 'app_countries', methods: ['GET'])]
    //   public function getAllCountries(): JsonResponse
    //    {
    //        $countries = $this->getDoctrine()->getRepository(Country::class)->findAll();

    //        return $this->json($countries);
    //    }
    //}
