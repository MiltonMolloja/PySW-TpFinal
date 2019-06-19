<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NovedadRepository")
 */
class Novedad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $asunto;

    /**
     * @ORM\Column(type="text")
     */
    private $mensaje;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Escribano", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $escribano;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getAsunto(): ?string
    {
        return $this->asunto;
    }

    public function setAsunto(string $asunto): self
    {
        $this->asunto = $asunto;

        return $this;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): self
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function getEscribano(): ?Escribano
    {
        return $this->escribano;
    }

    public function setEscribano(Escribano $escribano): self
    {
        $this->escribano = $escribano;

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
