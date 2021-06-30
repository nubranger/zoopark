<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Manager;
use App\Entity\Species;
use App\Service\UploaderHelper;
use DateTime;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AnimalController extends AbstractController
{
    /**
     * @Route("/", name="animal_index")
     */
    public function index(): Response
    {
        $animals = $this->getDoctrine()
            ->getRepository(Animal::class)
            ->findAll();

        return $this->render('animal/index.html.twig', [
            'animals' => $animals,
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
     * @Route("/animal/edit/{id}", name="animal_edit", methods={"GET"})
     */
    public function edit(Request $r, $id): Response
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
     */
    public function update(Request $r, ValidatorInterface $validator, $id, UploaderHelper $uploaderHelper): Response
    {
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
//                $r->getSession()->getFlashBag()->add('errors', $violation->getMessage());
                $this->addFlash('errors', $violation->getMessage());
                return $this->redirectToRoute('animal_edit', ['id' => $animal . $id]);
            }
            $newFilename = $uploaderHelper->uploadBookImage($uploadedFile);
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

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($animal);
        $entityManager->flush();

        return $this->redirectToRoute('animal_index');
    }

    /**
     * @Route("/animal/delete/{id}", name="animal_delete", methods={"POST"})
     */
    public
    function delete(Request $r, $id): Response
    {
        $book = $this->getDoctrine()
            ->getRepository(Animal::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('animal_index');
    }
}
