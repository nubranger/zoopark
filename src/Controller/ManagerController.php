<?php

namespace App\Controller;

use App\Entity\Manager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManagerController extends AbstractController
{
    /**
     * @Route("/manager", name="manager_index")
     */
    public function index(): Response
    {
        $managers = $this->getDoctrine()
            ->getRepository(Manager::class)
            ->findAll();

        return $this->render('manager/index.html.twig', [
            'managers' => $managers
        ]);
    }
}
