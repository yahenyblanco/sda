<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ventas
 *
 * @ORM\Table(name="ventas", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class Ventas
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="int", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="nroVenta", type="int", length=200, nullable=false)
     */
    private $nroVenta;

    /**
     * @var int
     *
     * @ORM\Column(name="idCliente", type="int", length=200, nullable=false)
     */
    private $idCliente;

    /**
     * @var int
     *
     * @ORM\Column(name="idCuenta", type="int", length=200, nullable=true)
     */
    private $idCuenta;

    /**
     * @var int
     *
     * @ORM\Column(name="idEquipo", type="int", length=200, nullable=false)
     */
    private $idEquipo;

    /**
     * @var int
     *
     * @ORM\Column(name="cantEquipo", type="int", precision=10, scale=0, nullable=false)
     */
    private $cantEquipo;


    /**
     * @var double
     *
     * @ORM\Column(name="descuento", type="double", precision=10, scale=0, nullable=false)
     */
    private $descuento;


    /**
     * @var double
     *
     * @ORM\Column(name="costoEntrega", type="double", precision=10, scale=0, nullable=false)
     */
    private $costoEntrega;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", precision=10, scale=0, nullable=false)
     */
    private $status;



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
     * Set nroVenta.
     *
     * @param int $nroVenta
     *
     * @return Ventas
     */
    public function setNroVenta($nroVenta)
    {
        $this->nroVenta = $nroVenta;

        return $this;
    }

    /**
     * Get nroVenta.
     *
     * @return int
     */
    public function getNroVenta()
    {
        return $this->nroVenta;
    }

    /**
     * Set idCliente.
     *
     * @param int $idCliente
     *
     * @return Ventas
     */
    public function setIdCliente($idCliente)
    {
        $this->idCliente = $idCliente;

        return $this;
    }

    /**
     * Get idCliente.
     *
     * @return int
     */
    public function getIdCliente()
    {
        return $this->idCliente;
    }

    /**
     * Set status.
     *
     * @param string|null $status
     *
     * @return Ventas
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set idCuenta.
     *
     * @param int $idCuenta
     *
     * @return Ventas
     */
    public function setIdCuenta($idCuenta)
    {
        $this->idCuenta = $idCuenta;

        return $this;
    }

    /**
     * Get idCuenta.
     *
     * @return int
     */
    public function getIdCuenta()
    {
        return $this->idCuenta;
    }

    /**
     * Set idEquipo.
     *
     * @param int $idEquipo
     *
     * @return Ventas
     */
    public function setIdEquipo($idEquipo)
    {
        $this->idEquipo = $idEquipo;

        return $this;
    }

    /**
     * Get idEquipo.
     *
     * @return int
     */
    public function getIdEquipo()
    {
        return $this->idEquipo;
    }


     /**
     * Set cantEquipo.
     *
     * @param int $cantEquipo
     *
     * @return Ventas
     */
    public function setCantEquipo($cantEquipo)
    {
        $this->cantEquipo = $cantEquipo;

        return $this;
    }

    /**
     * Get cantEquipo.
     *
     * @return int
     */
    public function getCantEquipo()
    {
        return $this->cantEquipo;
    }


     /**
     * Set descuento.
     *
     * @param double $descuento
     *
     * @return Ventas
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento.
     *
     * @return double
     */
    public function getDescuento()
    {
        return $this->descuento;
    }


    /**
     * Set costoEntrega.
     *
     * @param double $costoEntrega
     *
     * @return Ventas
     */
    public function setCostoEntrega($costoEntrega)
    {
        $this->costoEntrega = $costoEntrega;

        return $this;
    }

    /**
     * Get costoEntrega.
     *
     * @return double
     */
    public function getCostoEntrega()
    {
        return $this->costoEntrega;
    }





}
