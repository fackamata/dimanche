<?php

namespace App\Entity;

use App\Interfaces\FilableInterface;
use App\Repository\ConseilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConseilRepository::class)
 */
class Conseil implements FilableInterface
{
    public const FILE_DIR = '/upload/conseil';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePublication;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombreVue;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="conseil")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="conseil")
     */
    private $avis;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    public function __construct()
    {
        $this->avis = new ArrayCollection();
        /* date de publication rentrer en automatique à la date du jour */
        $this->datePublication = new \DateTime();
        /* on initialise le nombre de vue à  0*/
        $this->nombreVue = 0;
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

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

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): self
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getNombreVue(): ?int
    {
        return $this->nombreVue;
    }

    public function setNombreVue(int $nombreVue): self
    {
        $this->nombreVue = $nombreVue;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Avis[]
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis[] = $avi;
            $avi->setConseil($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getConseil() === $this) {
                $avi->setConseil(null);
            }
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
    
    public function getFileDirectory(): string
    {
        return self::FILE_DIR;
    }
}
