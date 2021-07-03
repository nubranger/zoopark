<?php

namespace App\Controller;

use App\Entity\Manager;
use App\Entity\Species;
use App\Service\UploaderHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ManagerController extends AbstractController
{
    /**
     * @Route("/manager", name="manager_index")
     */
    public function managerIndex(): Response
    {
        $managers = $this->getDoctrine()
            ->getRepository(Manager::class)
            ->findAll();

        return $this->render('manager/index.html.twig', [
            'managers' => $managers
        ]);
    }

    /**
     * @Route("/manager/create", name="manager_create", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function managerCreate(): Response
    {
        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->findAll();

        return $this->render('manager/create.html.twig', [
            'species' => $species,
        ]);
    }

    /**
     * @Route("/manager/store", name="manager_store", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function managerStore(Request $r, ValidatorInterface $validator, UploaderHelper $uploaderHelper): Response
    {
        $submittedToken = $r->request->get('token');
        if (!$this->isCsrfTokenValid('', $submittedToken)) {
            $this->addFlash('errors', 'Invalid token.');
        }

        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->find($r->request->get('manager_species_id'));

        $manager = new Manager();

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
                return $this->redirectToRoute('manager_create');
            }
            $newFilename = $uploaderHelper->uploadImage($uploadedFile, "/manager_images");
            $manager->setImage($newFilename);
        }

        $manager
            ->setName($r->request->get('manager_name'))
            ->setSurname($r->request->get('manager_surname'))
            ->setAbout($r->request->get('manager_about'))
            ->setSpecies($species);

        $errors = $validator->validate($manager);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('errors', $error->getMessage());
            }
            return $this->redirectToRoute('manager_create');
        }

        if (!$this->isCsrfTokenValid('', $submittedToken)) {
            return $this->redirectToRoute('manager_create');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($manager);
        $entityManager->flush();

        $this->addFlash('success', "Specialist {$manager->getName()} {$manager->getSurname()} was added.");

        return $this->redirectToRoute('manager_index');
    }

    /**
     * @Route("/manager/edit/{id}", name="manager_edit", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function managerEdit($id): Response
    {
        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->findAll();

        $manager = $this->getDoctrine()
            ->getRepository(Manager::class)
            ->find($id);

        return $this->render('manager/edit.html.twig', [
            'manager' => $manager,
            'species' => $species
        ]);
    }

    /**
     * @Route("/manager/update/{id}", name="manager_update", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function managerUpdate(Request $r, ValidatorInterface $validator, $id, UploaderHelper $uploaderHelper): Response
    {
        $submittedToken = $r->request->get('token');
        if (!$this->isCsrfTokenValid('', $submittedToken)) {
            $this->addFlash('errors', 'Invalid token.');
        }

        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->find($r->request->get('manager_species_id'));

        $manager = $this->getDoctrine()
            ->getRepository(Manager::class)
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
                return $this->redirectToRoute('manager_edit', ['id' => $manager . $id]);
            }
            $newFilename = $uploaderHelper->uploadImage($uploadedFile, "/manager_images");
            $manager->setImage($newFilename);
        }

        $manager
            ->setName($r->request->get('manager_name'))
            ->setSurname($r->request->get('manager_surname'))
            ->setAbout($r->request->get('manager_about'))
            ->setSpecies($species);

        $errors = $validator->validate($manager);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('errors', $error->getMessage());
            }
            return $this->redirectToRoute('manager_edit', ['id' => $id]);
        }

        if (!$this->isCsrfTokenValid('', $submittedToken)) {
            return $this->redirectToRoute('manager_edit', ['id' => $id]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($manager);
        $entityManager->flush();

        $this->addFlash('success', "Manager {$manager->getName()} {$manager->getSurname()} was edited.");

        return $this->redirectToRoute('manager_index');
    }

    /**
     * @Route("/manager/delete/{id}", name="manager_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function managerDelete(Request $r, $id): Response
    {
        $manager = $this->getDoctrine()
            ->getRepository(Manager::class)
            ->find($id);

        if ($manager->getAnimals()->count() > 0) {
            $this->addFlash('danger', "Can't delete: {$manager->getName()} {$manager->getSurname()} related to {$manager->getAnimals()->count()} animals.");
            return $this->redirectToRoute('manager_index');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($manager);
        $entityManager->flush();

        $this->addFlash('danger', "Manager {$manager->getName()} {$manager->getSurname()} was deleted.");

        return $this->redirectToRoute('manager_index');
    }

    /**
     * @Route("/manager/{id}", name="manager_view", methods={"GET"})
     */
    public function managerView($id): Response
    {
        $manager = $this->getDoctrine()
            ->getRepository(Manager::class)
            ->find($id);

        return $this->render('manager/manager.html.twig', [
            'manager' => $manager,
        ]);
    }
}
