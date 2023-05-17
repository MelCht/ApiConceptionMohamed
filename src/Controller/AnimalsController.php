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

class AnimalsController extends AbstractController
{
    // Récupérer la liste de tous les animaux:
    #[Route('/animals/all', name: 'app_animals', methods: ['GET'])]
    public function getAllAnimals(): JsonResponse
    {
        dd("Test");
        $animals = $this->getDoctrine()->getRepository(Animal::class)->findAll();

        dd($animals);
        return $this->json($animals);
    }

    // Récupérer un animal par son id:
    #[Route('/animals/{id}', name: 'app_animal', methods: ['GET'])]
    public function getAnimalById(int $id): JsonResponse
    {
        $animal = $this->getDoctrine()->getRepository(Animal::class)->find($id);

        if (!$animal) {
            return $this->json(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($animal);
    }

    // Récupérer la liste des animaux appartenant à un pays:
    #[Route('/animals/country/{countryId}', name: 'app_animals_by_country', methods: ['GET'])]
    public function getAnimalsByCountry(int $countryId): JsonResponse
    {
        $country = $this->getDoctrine()->getRepository(Country::class)->find($countryId);

        if (!$country) {
            return $this->json(['message' => 'Pays non disponible'], Response::HTTP_NOT_FOUND);
        }

        $animals = $country->getAnimals();

        return $this->json($animals);
    }

    // Ajouter un animal:
     #[Route('/animals/add', name: 'app_animals_add', methods: ['POST'])]
        public function create(Request $request): Response
        {
            $data = json_decode($request->getContent(), true);

            $validator = Validation::createValidator();
            $constraint = new Assert\Collection([
                'nom' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
                'tailleMoyenne' => [
                    new Assert\NotBlank(),
                    new Assert\Type('float'),
                ],
                'pays' => [
                    new Assert\NotBlank(),
                    new Assert\Type('integer'),
                ],
                'dureeVieMoyenne' => [
                    new Assert\NotBlank(),
                    new Assert\Type('float'),
                ],
                'artMartial' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
                'numeroTelephone' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ]);

            $violations = $validator->validate($data, $constraint);

            if (count($violations) > 0) {
                return $this->json(['errors' => $violations], Response::HTTP_BAD_REQUEST);
            }

            // Creée un nouvel animal
            $animal = new Animal();
            $animal->setNom($data['nom']);
            $animal->setTailleMoyenne($data['tailleMoyenne']);
            $animal->setPays($data['pays']);
            $animal->setDureeVieMoyenne($data['dureeVieMoyenne']);
            $animal->setArtMartial($data['artMartial']);
            $animal->setNumeroTelephone($data['numeroTelephone']);

            // Enregistre l'animal dans la base de donnée

            return $this->json(['message' => 'Animal créé'], Response::HTTP_CREATED);
        }

    // Supprimer un animal par son id:
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

    // Mettre à jour un animal:
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
           $animal->setPays($data['pays']);
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

       // Enregistre l'animal mis à jour dans la bdd

       return $this->json(['message' => 'Animal mis à jour']);
   }


    // Mettre à jour le pays d’un animal:
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

        return $this->json(['message' => 'Pays de l\'animal mis à jour']);
    }
}
