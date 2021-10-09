<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AvisRepository::class)
 */
class Avis
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePublication;

    // /**
    //  * @ORM\ManyToOne(targetEntity=Note::class, inversedBy="avis")
    //  */
    // private $note;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="avis")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Conseil::class, inversedBy="avis")
     */
    private $conseil;

    public function __construct()
    {
        $this->datePublication = new \DateTime();
    }

  
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

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

    // public function getNote(): ?Note
    // {
    //     return $this->note;
    // }

    // public function setNote(?Note $note): self
    // {
    //     $this->note = $note;

    //     return $this;
    // }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getConseil(): ?Conseil
    {
        return $this->conseil;
    }

    public function setConseil(?Conseil $conseil): self
    {
        $this->conseil = $conseil;

        return $this;
    }


}
