<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
class Etudiant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de naissance est obligatoire.")]
    #[Assert\LessThan("today", message: "La date de naissance doit être dans le passé.")]
    private ?\DateTime $naissance = null;

    #[ORM\Column(nullable: true)]
    private ?int $niveau = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "Adresse email non valide : '{{ value }}'.")]
    private ?string $mail = null;

    /**
     * @var Collection<int, Absence>
     */
    #[ORM\OneToMany(targetEntity: Absence::class, mappedBy: 'etudiant')]
    private Collection $absences;

    public function __construct()
    {
        $this->absences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNaissance(): ?\DateTime
    {
        return $this->naissance;
    }

    public function setNaissance(\DateTime $naissance): static
    {
        $this->naissance = $naissance;

        return $this;
    }

    public function getNiveau(): ?int
    {
        return $this->niveau;
    }

    public function setNiveau(?int $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }
    public function getMail(): ?string
{
    return $this->mail;
}

public function setMail(?string $mail): static
{
    $this->mail = $mail;

    return $this;
}

/**
 * @return Collection<int, Absence>
 */
public function getAbsences(): Collection
{
    return $this->absences;
}

public function addAbsence(Absence $absence): static
{
    if (!$this->absences->contains($absence)) {
        $this->absences->add($absence);
        $absence->setEtudiant($this);
    }

    return $this;
}

public function removeAbsence(Absence $absence): static
{
    if ($this->absences->removeElement($absence)) {
        // set the owning side to null (unless already changed)
        if ($absence->getEtudiant() === $this) {
            $absence->setEtudiant(null);
        }
    }

    return $this;
}
public function __toString()
{
    return $this->nom;
}
}
