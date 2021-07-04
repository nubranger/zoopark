<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Manager;
use App\Entity\Species;
use App\Service\ImageDeleteService;
use App\Service\ImageUploadService;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Filesystem\Filesystem;

class AnimalController extends AbstractController
{
    /**
     * @Route("/", name="animal_index")
     */
    public function animalIndex(Request $r, AuthenticationUtils $authenticationUtils): Response
    {
        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->findAll();

        $animals = $this->getDoctrine()
            ->getRepository(Animal::class);

        if ($r->query->get('sort') === 'name_az') {
            $animals = $animals->findBy([], ['name' => 'asc']);
        } elseif ($r->query->get('sort') === 'name_za') {
            $animals = $animals->findBy([], ['name' => 'desc']);
        } elseif ($r->query->get('sort') === 'year_lh') {
            $animals = $animals->findBy([], ['birth_year' => 'asc']);
        } elseif ($r->query->get('sort') === 'year_hl') {
            $animals = $animals->findBy([], ['birth_year' => 'desc']);
        } elseif ($r->query->get('species_id') !== null && $r->query->get('species_id') != 0) {
            $specie = $this->getDoctrine()->
            getRepository(Species::class)->
            find($r->query->get('species_id'));
            $animals = $specie->getAnimals();
        } elseif ($r->query->get('species_id') === 0) {
            $animals = $animals->findAll();
        } else {
            $animals = $animals->findAll();
        }

        return $this->render('animal/index.html.twig', [
            'animals' => $animals,
            'species' => $species,
            'specieId' => $r->query->get('species_id') ?? 0,
            'sortBy' => $r->query->get('sort') ?? 'default'
        ]);
    }

    /**
     * @Route("/animal/create", name="animal_create", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function animalCreate(): Response
    {
        $managers = $this->getDoctrine()
            ->getRepository(Manager::class)
            ->findAll();

        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->findAll();

        return $this->render('animal/create.html.twig', [
            'managers' => $managers,
            'species' => $species,
//            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
        ]);
    }

    /**
     * @Route("/animal/{id}", name="animal_view", methods={"GET"})
     */
    public function animalView($id): Response
    {
        $animal = $this->getDoctrine()
            ->getRepository(Animal::class)
            ->find($id);

        return $this->render('animal/animal.html.twig', [
            'animal' => $animal,
        ]);
    }


    /**
     * @Route("/animal/store", name="animal_store", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function animalStore(Request $r, ValidatorInterface $validator, ImageUploadService $imageUploadService): Response
    {
        $submittedToken = $r->request->get('token');
        if (!$this->isCsrfTokenValid('', $submittedToken)) {
            $this->addFlash('errors', 'Invalid token.');
        }

        $manager = $this->getDoctrine()
            ->getRepository(Manager::class)
            ->find($r->request->get('animal_manager_id'));

        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->find($r->request->get('animal_species_id'));

        $animal = new Animal();

        $uploadedFile = $r->files->get('image');
        if ($uploadedFile) {

            $violations = $validator->validate(
                $uploadedFile,
                new File([
                    'maxSize' => '1M',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg'
//                        'image/*'
                    ]
                ])
            );

            if ($violations->count() > 0) {
                $violation = $violations[0];
//                $r->getSession()->getFlashBag()->add('errors', $violation->getMessage());
                $this->addFlash('errors', $violation->getMessage());
                return $this->redirectToRoute('animal_create');
            }
            $newFilename = $imageUploadService->uploadImage($uploadedFile, "/animal_images");
            $animal->setImage($newFilename);
        }

        $date = new DateTime($r->request->get('animal_birthyear'));
        $animal
            ->setName($r->request->get('animal_name'))
            ->setBirthYear($date)
            ->setAnimalBook($r->request->get('animal_book'))
            ->setSpecies($species)
            ->setManager($manager);

        $errors = $validator->validate($animal);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
//                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
                $this->addFlash('errors', $error->getMessage());
            }
            return $this->redirectToRoute('animal_create');
        }

        if (!$this->isCsrfTokenValid('', $submittedToken)) {
            return $this->redirectToRoute('animal_create');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($animal);
        $entityManager->flush();

        $this->addFlash('success', "Animal {$animal->getName()} was added.");

        return $this->redirectToRoute('animal_index');
    }

    /**
     * @Route("/animal/edit/{id}", name="animal_edit", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function animalEdit($id): Response
    {
        $managers = $this->getDoctrine()
            ->getRepository(Manager::class)
            ->findAll();

        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->findAll();

        $animal = $this->getDoctrine()
            ->getRepository(Animal::class)
            ->find($id);

        return $this->render('animal/edit.html.twig', [
            'animal' => $animal,
            'managers' => $managers,
            'species' => $species,
//            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
        ]);
    }

    /**
     * @Route("/animal/update/{id}", name="animal_update", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function animalUpdate(Request $r, ValidatorInterface $validator, $id, ImageDeleteService $imageDeleteService, ImageUploadService $imageUploadService): Response
    {
        $submittedToken = $r->request->get('token');
        if (!$this->isCsrfTokenValid('', $submittedToken)) {
            $this->addFlash('errors', 'Invalid token.');
        }

        $manager = $this->getDoctrine()
            ->getRepository(Manager::class)
            ->find($r->request->get('animal_manager_id'));

        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->find($r->request->get('animal_species_id'));

        $animal = $this->getDoctrine()
            ->getRepository(Animal::class)
            ->find($id);

        $uploadedFile = $r->files->get('image');
        if ($uploadedFile) {

            $violations = $validator->validate(
                $uploadedFile,
                new File([
                    'maxSize' => '1M',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg'
//                        'image/*'
                    ]
                ])
            );

            if ($violations->count() > 0) {
                $violation = $violations[0];
                $this->addFlash('errors', $violation->getMessage());
                return $this->redirectToRoute('animal_edit', ['id' => $animal . $id]);
            }

            $imageDeleteService->deleteImage($animal->getImagePath());

            $newFilename = $imageUploadService->uploadImage($uploadedFile, "/animal_images");
            $animal->setImage($newFilename);
        }

        $date = new DateTime($r->request->get('animal_birthyear'));
        $animal
            ->setName($r->request->get('animal_name'))
            ->setBirthYear($date)
            ->setAnimalBook($r->request->get('animal_book'))
            ->setSpecies($species)
            ->setManager($manager);

        $errors = $validator->validate($animal);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
//                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
                $this->addFlash('errors', $error->getMessage());
            }
            return $this->redirectToRoute('animal_edit', ['id' => $id]);
        }

        if (!$this->isCsrfTokenValid('', $submittedToken)) {
            return $this->redirectToRoute('animal_edit', ['id' => $id]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($animal);
        $entityManager->flush();

        $this->addFlash('success', "Animal {$animal->getName()} was edited.");

        return $this->redirectToRoute('animal_index');
    }

    /**
     * @Route("/animal/delete/{id}", name="animal_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function animalDelete($id, ImageDeleteService $imageDeleteService): Response
    {
        $animal = $this->getDoctrine()
            ->getRepository(Animal::class)
            ->find($id);

        $imageDeleteService->deleteImage($animal->getImagePath());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($animal);
        $entityManager->flush();

        $this->addFlash('danger', "Animal {$animal->getName()} was deleted.");

        return $this->redirectToRoute('animal_index');
    }
}
