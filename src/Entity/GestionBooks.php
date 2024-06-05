<?php

namespace App\Entity;

use App\Repository\GestionBooksRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GestionBooksRepository::class)]
class GestionBooks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'gestionBooks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\ManyToOne(inversedBy: 'gestionBooks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $DateSortie = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $DateRentre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeImmutable
    {
        return $this->DateSortie;
    }

    public function setDateSortie(\DateTimeImmutable $DateSortie): static
    {
        $this->DateSortie = $DateSortie;

        return $this;
    }

    public function getDateRentre(): ?\DateTimeImmutable
    {
        return $this->DateRentre;
    }

    public function setDateRentre(?\DateTimeImmutable $DateRentre): static
    {
        $this->DateRentre = $DateRentre;

        return $this;
    }
}
