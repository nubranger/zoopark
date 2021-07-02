<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Species;
use App\Service\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SpeciesController extends AbstractController
{
    /**
     * @Route("/species", name="species_index")
     */
    public function speciesIndex(): Response
    {
        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->findAll();

        return $this->render('species/index.html.twig', [
            'species' => $species,
        ]);
    }

    /**
     * @Route("/species/create", name="species_create", methods={"GET"})
     */
    public function speciesCreate(): Response
    {
        return $this->render('species/create.html.twig', [
//            'test' => $test,
        ]);
    }

//    /**
//     * @Route("/species/{id}", name="species_animals")
//     */
//    public function speciesView($id): Response
//    {
//        $species = $this->getDoctrine()
//            ->getRepository(Species::class)
//            ->find($id);
//
//        $animals = $species->getAnimals();
//
//        return $this->render('species/animals.html.twig', [
//            'animals' => $animals,
//            'species' => $species
//        ]);
//    }

    /**
     * @Route("/species/store", name="species_store", methods={"POST"})
     */
    public function speciesStore(Request $r, ValidatorInterface $validator, UploaderHelper $uploaderHelper): Response
    {
        $species = new Species();

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
                return $this->redirectToRoute('species_create');
            }
            $newFilename = $uploaderHelper->uploadImage($uploadedFile, "/species_images");
            $species->setImage($newFilename);
        }

        $species
            ->setName($r->request->get('species_name'))
            ->setAbout($r->request->get('species_about'));

        $errors = $validator->validate($species);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('errors', $error->getMessage());
            }
            return $this->redirectToRoute('species_create');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($species);
        $entityManager->flush();

        $this->addFlash('success', "Species {$species->getName()} was added.");

        return $this->redirectToRoute('species_index');
    }

    /**
     * @Route("/species/edit/{id}", name="species_edit", methods={"GET"})
     */
    public function speciesEdit($id): Response
    {
        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->find($id);

        return $this->render('species/edit.html.twig', [
            'species' => $species
        ]);
    }

    /**
     * @Route("/species/update/{id}", name="species_update", methods={"POST"})
     */
    public function speciesUpdate(Request $r, ValidatorInterface $validator, $id, UploaderHelper $uploaderHelper): Response
    {
        $species = $this->getDoctrine()
            ->getRepository(Species::class)
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
                return $this->redirectToRoute('species_edit', ['id' => $species . $id]);
            }
            $newFilename = $uploaderHelper->uploadImage($uploadedFile, "/species_images");
            $species->setImage($newFilename);
        }

        $species
            ->setName($r->request->get('species_name'))
            ->setAbout($r->request->get('species_about'));

        $errors = $validator->validate($species);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('errors', $error->getMessage());
            }
            return $this->redirectToRoute('species_edit', ['id' => $id]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($species);
        $entityManager->flush();

        $this->addFlash('success', "Species {$species->getName()} was edited.");

        return $this->redirectToRoute('species_index');
    }

    /**
     * @Route("/species/delete/{id}", name="species_delete", methods={"POST"})
     */
    public function speciesDelete(Request $r, $id): Response
    {
        $manager = $this->getDoctrine()
            ->getRepository(Species::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($manager);
        $entityManager->flush();

        $this->addFlash('danger', "Species {$manager->getName()} was deleted.");

        return $this->redirectToRoute('species_index');
    }
}
