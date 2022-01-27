<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FilmRepository::class)
 */
class Film
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255, maxMessage="Votre titre est trop long !")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10, maxMessage="Une description ne peut pas être aussi courte")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $note;

    /**
     * @ORM\Column(type="integer", nullable="true")
     * @Assert\IsNull
     */
    private $nb_votants;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getNb_votants(): ?int
    {
        return $this->nb_votants;
    }

    public function setNb_votants(int $nb_votants): self
    {
        return $nb_votants++;
    }
}
