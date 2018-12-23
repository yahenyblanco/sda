<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recolecciones
 *
 * @ORM\Table(name="recolecciones")
 * @ORM\Entity
 */
class Recolecciones
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="pedidos", type="integer", nullable=false)
     */
    private $pedidos;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha_recoleccion", type="date", length=100, nullable=false)
     */
    private $fecha_recoleccion;




    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pedidos.
     *
     * @param int $pedidos
     *
     * @return Recolecciones
     */
    public function setPedidos($pedidos)
    {
        $this->pedidos = $pedidos;

        return $this;
    }

    /**
     * Get pedidos.
     *
     * @return int
     */
    public function getPedidos()
    {
        return $this->pedidos;
    }

    /**
     * Set fecha_recoleccion.
     *
     * @param date $fecha_recoleccion
     *
     * @return Recolecciones
     */
    public function setFecha_recoleccion($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha_recoleccion.
     *
     * @return date
     */
    public function getFecha_recoleccion()
    {
        return $this->fecha;
    }

   
}
