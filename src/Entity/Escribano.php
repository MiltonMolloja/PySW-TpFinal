<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EscribanoRepository")
 */
class Escribano
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $matricula;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $universidad;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Escribania")
     * @ORM\JoinColumn(nullable=false)
     */
    private $escribania;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatricula(): ?int
    {
        return $this->matricula;
    }

    public function setMatricula(int $matricula): self
    {
        $this->matricula = $matricula;

        return $this;
    }

    public function getUniversidad(): ?string
    {
        return $this->universidad;
    }

    public function setUniversidad(string $universidad): self
    {
        $this->universidad = $universidad;

        return $this;
    }

    public function getEscribania(): ?Escribania
    {
        return $this->escribania;
    }

    public function setEscribania(?Escribania $escribania): self
    {
        $this->escribania = $escribania;

        return $this;
    }

    public function getEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): self
    {
        $this->estado = $estado;

        return $this;
    }
}
