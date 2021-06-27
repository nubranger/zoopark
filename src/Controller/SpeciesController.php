<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Species;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpeciesController extends AbstractController
{
    /**
     * @Route("/species", name="species_index")
     */
    public function index(): Response
    {
        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->findAll();

        return $this->render('species/index.html.twig', [
            'species' => $species,
        ]);
    }

    /**
     * @Route("/species/{id}", name="species_animals")
     */
    public function specie($id): Response
    {
        $species = $this->getDoctrine()
            ->getRepository(Species::class)
            ->find($id);

        $animals = $species->getAnimals();

        return $this->render('species/animals.html.twig', [
            'animals' => $animals,
            'species' => $species
        ]);
    }
}
