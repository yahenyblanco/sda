<?php

namespace GestionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PedidosArchivados
 *
* @ORM\Table(name="pedidos_archivados", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class PedidosArchivados
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
     * @ORM\Column(name="pedido", type="integer", nullable=false)
     */
    private $pedido;

    /**
     * @var string
     *
     * @ORM\Column(name="folio", type="string", length=100, nullable=false)
     */
    private $folio;

    /**
     * @var string
     *
     * @ORM\Column(name="cliente", type="string", length=100, nullable=false)
     */
    private $cliente;

    /**
     * @var string
     *
     * @ORM\Column(name="cuenta", type="string", length=100, nullable=false)
     */
    private $cuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha", type="string", length=100, nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="devolucion", type="string", length=100, nullable=false)
     */
    private $devolucion;

    /**
     * @var int
     *
     * @ORM\Column(name="cant", type="integer", nullable=false)
     */
    private $cant;

    /**
     * @var string
     *
     * @ORM\Column(name="clave", type="string", length=500, nullable=false)
     */
    private $clave;

    /**
     * @var string
     *
     * @ORM\Column(name="equipo", type="string", length=500, nullable=false)
     */
    private $equipo;

    /**
     * @var int
     *
     * @ORM\Column(name="dias", type="integer", nullable=false)
     */
    private $dias;

    /**
     * @var float
     *
     * @ORM\Column(name="PU", type="float", precision=10, scale=0, nullable=false)
     */
    private $pu;

    /**
     * @var float
     *
     * @ORM\Column(name="importe", type="float", precision=10, scale=0, nullable=false)
     */
    private $importe;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion_entrega", type="string", length=200, nullable=false)
     */
    private $direccionEntrega;

    /**
     * @var string
     *
     * @ORM\Column(name="comentarios", type="string", length=200, nullable=false)
     */
    private $comentarios;

    /**
     * @var float
     *
     * @ORM\Column(name="descuento", type="float", precision=10, scale=0, nullable=false)
     */
    private $descuento;

    /**
     * @var float
     *
     * @ORM\Column(name="impuesto", type="float", precision=10, scale=0, nullable=false)
     */
    private $impuesto;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", precision=10, scale=0, nullable=false)
     */
    private $total;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal", type="float", precision=10, scale=0, nullable=false)
     */
    private $subtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal2", type="float", precision=10, scale=0, nullable=false)
     */
    private $subtotal2;

    /**
     * @var float
     *
     * @ORM\Column(name="servicioentrega", type="float", precision=10, scale=0, nullable=false)
     */
    private $servicioentrega;

  
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
     * Set pedido.
     *
     * @param int $pedido
     *
     * @return Pedidos
     */
    public function setPedido($pedido)
    {
        $this->pedido = $pedido;

        return $this;
    }

    /**
     * Get pedido.
     *
     * @return int
     */
    public function getPedido()
    {
        return $this->pedido;
    }

    /**
     * Set folio.
     *
     * @param string $folio
     *
     * @return Pedidos
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;

        return $this;
    }

    /**
     * Get folio.
     *
     * @return string
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * Set cliente.
     *
     * @param string $cliente
     *
     * @return Pedidos
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente.
     *
     * @return string
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set cuenta.
     *
     * @param string $cuenta
     *
     * @return Pedidos
     */
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;

        return $this;
    }

    /**
     * Get cuenta.
     *
     * @return string
     */
    public function getCuenta()
    {
        return $this->cuenta;
    }

    /**
     * Set fecha.
     *
     * @param string $fecha
     *
     * @return Pedidos
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha.
     *
     * @return string
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set devolucion.
     *
     * @param string $devolucion
     *
     * @return Pedidos
     */
    public function setDevolucion($devolucion)
    {
        $this->devolucion = $devolucion;

        return $this;
    }

    /**
     * Get devolucion.
     *
     * @return string
     */
    public function getDevolucion()
    {
        return $this->devolucion;
    }

    /**
     * Set cant.
     *
     * @param int $cant
     *
     * @return Pedidos
     */
    public function setCant($cant)
    {
        $this->cant = $cant;

        return $this;
    }

    /**
     * Get cant.
     *
     * @return int
     */
    public function getCant()
    {
        return $this->cant;
    }

    /**
     * Set clave.
     *
     * @param string $clave
     *
     * @return Pedidos
     */
    public function setClave($clave)
    {
        $this->clave = $clave;

        return $this;
    }

    /**
     * Get clave.
     *
     * @return string
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Set equipo.
     *
     * @param string $equipo
     *
     * @return Pedidos
     */
    public function setEquipo($equipo)
    {
        $this->equipo = $equipo;

        return $this;
    }

    /**
     * Get equipo.
     *
     * @return string
     */
    public function getEquipo()
    {
        return $this->equipo;
    }

    /**
     * Set dias.
     *
     * @param int $dias
     *
     * @return Pedidos
     */
    public function setDias($dias)
    {
        $this->dias = $dias;

        return $this;
    }

    /**
     * Get dias.
     *
     * @return int
     */
    public function getDias()
    {
        return $this->dias;
    }

    /**
     * Set pu.
     *
     * @param float $pu
     *
     * @return Pedidos
     */
    public function setPu($pu)
    {
        $this->pu = $pu;

        return $this;
    }

    /**
     * Get pu.
     *
     * @return float
     */
    public function getPu()
    {
        return $this->pu;
    }

    /**
     * Set importe.
     *
     * @param float $importe
     *
     * @return Pedidos
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe.
     *
     * @return float
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set direccionEntrega.
     *
     * @param string $direccionEntrega
     *
     * @return Pedidos
     */
    public function setDireccionEntrega($direccionEntrega)
    {
        $this->direccionEntrega = $direccionEntrega;

        return $this;
    }

    /**
     * Get direccionEntrega.
     *
     * @return string
     */
    public function getDireccionEntrega()
    {
        return $this->direccionEntrega;
    }

    /**
     * Set comentarios.
     *
     * @param string $comentarios
     *
     * @return Pedidos
     */
    public function setComentarios($comentarios)
    {
        $this->comentarios = $comentarios;

        return $this;
    }

    /**
     * Get comentarios.
     *
     * @return string
     */
    public function getComentarios()
    {
        return $this->comentarios;
    }

    /**
     * Set descuento.
     *
     * @param float $descuento
     *
     * @return Pedidos
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento.
     *
     * @return float
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set impuesto.
     *
     * @param float $impuesto
     *
     * @return Pedidos
     */
    public function setImpuesto($impuesto)
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    /**
     * Get impuesto.
     *
     * @return float
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * Set total.
     *
     * @param float $total
     *
     * @return Pedidos
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set subtotal.
     *
     * @param float $subtotal
     *
     * @return Pedidos
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal.
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set subtotal2.
     *
     * @param float $subtotal2
     *
     * @return Pedidos
     */
    public function setSubtotal2($subtotal2)
    {
        $this->subtotal2 = $subtotal2;

        return $this;
    }

    /**
     * Get subtotal2.
     *
     * @return float
     */
    public function getSubtotal2()
    {
        return $this->subtotal2;
    }

    /**
     * Set servicioentrega.
     *
     * @param float $servicioentrega
     *
     * @return Pedidos
     */
    public function setServicioentrega($servicioentrega)
    {
        $this->servicioentrega = $servicioentrega;

        return $this;
    }

    /**
     * Get servicioentrega.
     *
     * @return float
     */
    public function getServicioentrega()
    {
        return $this->servicioentrega;
    }
     public function __toString()
   {
      return strval($this->getId());
   }
}
