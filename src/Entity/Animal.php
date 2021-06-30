<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AnimalRepository::class)
 */
class Animal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Name should not be blank.")
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "Name must be at least {{ limit }} characters long.",
     *      maxMessage = "Name cannot be longer than {{ limit }} characters."
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="date")
     */
    private $birth_year;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $animal_book;

    /**
     * @ORM\ManyToOne(targetEntity=Manager::class, inversedBy="animals")
     */
    private $manager;

    /**
     * @ORM\ManyToOne(targetEntity=Species::class, inversedBy="animals")
     */
    private $species;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthYear(): ?\DateTimeInterface
    {
        return $this->birth_year;
    }

    public function setBirthYear(\DateTimeInterface $birth_year): self
    {
        $this->birth_year = $birth_year;

        return $this;
    }

    public function getAnimalBook(): ?string
    {
        return $this->animal_book;
    }

    public function setAnimalBook(?string $animal_book): self
    {
        $this->animal_book = $animal_book;

        return $this;
    }

    public function getManager(): ?Manager
    {
        return $this->manager;
    }

    public function setManager(?Manager $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    public function getSpecies(): ?Species
    {
        return $this->species;
    }

    public function setSpecies(?Species $species): self
    {
        $this->species = $species;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return '/uploads/animal_images/' . $this->getImage();
    }
}
