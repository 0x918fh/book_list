<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 */
class Author
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $fam;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $nam;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $ots;

    /**
     * @ORM\Column(type="integer")
     */
    private $bookCount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFam(): ?string
    {
        return $this->fam;
    }

    public function setFam(string $fam): self
    {
        $this->fam = $fam;

        return $this;
    }

    public function getNam(): ?string
    {
        return $this->nam;
    }

    public function setNam(string $nam): self
    {
        $this->nam = $nam;

        return $this;
    }

    public function getOts(): ?string
    {
        return $this->ots;
    }

    public function setOts(?string $ots): self
    {
        $this->ots = $ots;

        return $this;
    }

    public function getBookCount(): ?int
    {
        return $this->bookCount;
    }

    public function setBookCount(int $bookCount): self
    {
        $this->bookCount = $bookCount;

        return $this;
    }
}
