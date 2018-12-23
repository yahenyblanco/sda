<?php
namespace GestionBundle\Controller;
use GestionBundle\Entity\Ultpedido;
use GestionBundle\Entity\Pedidos;
use GestionBundle\Entity\Factura;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Pedido controller.
 *
 */
class PedidosController extends Controller
{
    /**
     * Lists all pedido entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pedidos = $em->getRepository('GestionBundle:Pedidos')->findAll();

        return $this->render('pedidos/index.html.twig', array(
            'pedidos' => $pedidos,
        ));
    }
    /**
     * Finds and displays a pedido entity.
     *
     */
    public function showAction(Pedidos $pedido)
    {
        return $this->render('pedidos/show.html.twig', array(
            'pedido' => $pedido,
        ));
    }
    
    public function busquedaAvanzadaAction(Request $request)
    {
       
    $session = $request->getSession(); 
            if(!$session->get("usuarionombre")){
                $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
                return $this->redirect($this->generateUrl('usuario_login'));
            }
            $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
            $clientes = $conn->query("SELECT c.razon_social,ct.domicilio,ct.cuenta_id FROM cuentas_cliente ct,clientes c where ct.cliente=c.cliente and c.desactivar=0")->fetchAll(); 
            return $this->render('pedidos/busquedaavanzada.html.twig', array(
            'clientes'=> $clientes
          ));
    }
    public function homeAction(Request $request)
    {
        $fecha=date("d/m/Y");
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        } 
      
              $manager = $this->getDoctrine()->getManager();
         $conn = $manager->getConnection();
         $montopedido=0;
         $datos= $conn->query("SELECT pedido from ultpedido")->fetchAll();
         $tpago=0;
//          for ($i=0;$i<count($datos); $i++)
//         {
//           $id=$datos[$i]['pedido'];
//             $montopd = $conn->query("SELECT montopedido FROM montospedidos where pedido=".$id." limit 1")->fetchAll(); 
//             $totalcargo= $conn->query("SELECT sum(p.montopago) as total from detalles_pago p where p.pedido=".$id." and   operacion='cargo'")->fetchAll(); 
//             if (count($montopd)> 0){
//               if (count($totalcargo)>0){
//             $totalmontopd=round($montopd[0]['montopedido'],2)+ round($totalcargo[0]['total'],2);

// //echo "/".round($montopd[0]['montopedido'],2)." / ".round($totalcargo[0]['total'],2);
// //echo "/".$totalmontopd;
// }}
// //exit();
//             $totalpagos= $conn->query("SELECT sum(p.montopago) as totalpago from detalles_pago p where p.pedido=".$id." and   operacion='pago'")->fetchAll(); 
//             $tpago=round($totalpagos[0]['totalpago'],2);

//             $porpg = $totalmontopd - $tpago;
//            $act=$conn->query("UPDATE pedidos SET montototalpago='$tpago', montototalpedido=$totalmontopd,por_pagar=$porpg WHERE pedido=".$id); 



     
//    }
    return $this->render('pedidos/home.html.twig');
  }
    public function entregarpedidosAction(Request $request)
    {
       $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
      $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();

    $pedidosconsl= $conn->query("SELECT p.pedido,p.folio,p.fecha,p.devolucion,p.cliente, s.status, est.statussaldos FROM status_saldos est,pedidos p, status_entrega s, montospedidos m WHERE p.status_pedido=s.id and p.status_pedido = '4' AND m.pedido=p.pedido and m.statussaldo=est.id")->fetchAll();


    return new JsonResponse($pedidosconsl);
    }


     public function cobrarpedidoAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
    $manager = $this->getDoctrine()->getManager();
    $conn = $manager->getConnection();
    $pedidoscon= $conn->query("SELECT DISTINCT m.pedido,c.cliente,c.cuenta,m.saldorestante,m.fecha,es.status,s.statussaldos,c.devolucion FROM pedidos c ,montospedidos m, status_saldos s, status_entrega es WHERE m.statussaldo = 1 AND m.pedido=c.pedido and m.statussaldo=s.id and c.status_pedido=es.id")->fetchAll();

    return new JsonResponse($pedidoscon);
   
    }


    public function recoleccionAction(Request $request){
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
        $pedido = ($_POST['pedido']);
        $recoleccion = ($_POST['fecha']);

        $año2 = substr($recoleccion, 6,4);
        $dia2 = substr($recoleccion,0,2);
        $mes2 = substr($recoleccion,3,2);
        $fecha2= $año2."-".$mes2."-".$dia2;
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();

        // $id= $conn->query("SELECT * FROM recolecciones where pedidos=".$pedido."")->fetchAll();

        //  if (count($id)== 0){
        //    $insertar = $conn->query("INSERT INTO recolecciones (pedidos,fecha_recoleccion) VALUES ($pedido,'$fecha2')");    
        //  }else{

           $act=$conn->query("UPDATE pedidos SET fecha_recoleccion='$fecha2' WHERE pedido=".$pedido.""); 
         //}  
    }

    public function facturarpedidosAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
    $manager = $this->getDoctrine()->getManager();
    $conn = $manager->getConnection();
$pedidoscn= $conn->query("SELECT DISTINCT d.id,d.pedido,d.fechapago,d.montopago,f.formas,d.foliofactura, s.status, est.statussaldos,p.devolucion FROM detalles_pago d,formas_pagos f,status_saldos est,status_entrega s,montospedidos m, pedidos p WHERE d.foliofactura ='Pendiente' and p.status_pedido=s.id and m.pedido=d.pedido and m.statussaldo=est.id and d.formapago=f.id and d.pedido= p.pedido")->fetchAll();

return new JsonResponse($pedidoscn);
    } 
  
    public function detallescotizacionesAction(Request $request,$cot)
    {
     $session = $request->getSession(); 
      if(!$session->get("usuarionombre")){
          $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
          return $this->redirect($this->generateUrl('usuario_login'));
      }

       $manager = $this->getDoctrine()->getManager();
       $conn = $manager->getConnection();
       $cots = $conn->query("SELECT p.cotizacion,p.pedido, p.devolucion,p.cliente,p.obra,p.fecha,p.status,p.cantidad,p.clave,p.equipo,p.dias,p.PU,p.importe,p.direccion_entrega,p.comentarios,p.descuento,p.impuesto,p.total,p.subtotal,p.subtotal2,p.servicioentrega,p.id FROM cotizaciones p  where p.cotizacion=".$cot)->fetchAll(); 
        return $this->render('pedidos/detallespedido.html.twig',array('cotizaciones' => $cots,'cot' => $cot));


    }public function detallesarchivoAction(Request $request,$id)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }

            $fechadevol='';
            $idpd=0;
            $montopago='';
            if($id<>0){ 
            $manager = $this->getDoctrine()->getManager();
            $conn = $manager->getConnection();
            $datosequipos = $conn->query("SELECT p.pedido,p.montototalpago,p.por_pagar, p.folio,p.devolucion,p.cliente,p.cuenta,p.fecha,s.status,p.cant,p.clave,p.equipo,p.dias,p.PU,p.importe,p.direccion_entrega,p.comentarios,p.descuento,p.impuesto,p.total,p.subtotal,p.subtotal2,p.servicioentrega,p.id FROM pedidos p, status_entrega s where p.status_pedido=s.id AND p.pedido=".$id)->fetchAll(); 
            $datosrecoleccion = $conn->query("SELECT * FROM recolecciones where pedidos=$id")->fetchAll(); 
           
            $datosbitacora = $conn->query("SELECT DISTINCT p.id, p.pedido,p.hora,p.comentarios,u.nombre_usuario, DATE_FORMAT(p.fecha,'%d/%m/%Y') as fecha FROM bitacora_pedido p, usuario u where p.usuario=u.id and p.pedido =".$id)->fetchAll(); 
            
            if(empty($datosequipos)){

              $datosequipos = $conn->query("SELECT p.pedido,p.montototalpago,p.por_pagar, p.folio,p.devolucion,p.cliente,p.cuenta,p.fecha,s.status,p.cant,p.clave,p.equipo,p.dias,p.PU,p.importe,p.direccion_entrega,p.comentarios,p.descuento,p.impuesto,p.total,p.subtotal,p.subtotal2,p.servicioentrega,p.id FROM pedidos_archivados p,status_entrega s where p.status_pedido=s.id and  p.pedido=".$id)->fetchAll(); 
            }
    return $this->render('pedidos/detallesarchivado.html.twig',array('datosequipos' => $datosequipos,'recolecciones' => $datosrecoleccion,'id' => $id,'cot' => '00','datosbitacora' => $datosbitacora));
   
    }if($id==0){ 
       $generado='1';
        $cont=0;
        $pedido=0;
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $datosequipos=array();
        $query2=array();
        $dts=array();
        $datosbitacora=array();
        
        return $this->render('pedidos/detallesarchivado.html.twig',array('datosequipos' => $datosequipos,'query2' => $query2,'dts' => $dts,'id' => $id,'cot'=>'00','montopagado' => '0','restante' => '0','datosbitacora' => '0'));
        
        
        }
    }  
    public function detallespedidoAction(Request $request,$id)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }

            $fechadevol='';
            $idpd=0;
            $montopago='';
            if($id<>0){ 
            $manager = $this->getDoctrine()->getManager();
            $conn = $manager->getConnection();
            $datosequipos = $conn->query("SELECT p.pedido,p.montototalpago,p.por_pagar, p.folio,p.devolucion,p.cliente,p.cuenta,p.fecha,s.status,p.cant,p.clave,p.equipo,p.dias,p.PU,p.importe,p.direccion_entrega,p.comentarios,p.descuento,p.impuesto,p.total,p.subtotal,p.subtotal2,p.servicioentrega,p.id FROM pedidos p, status_entrega s where p.status_pedido=s.id AND p.pedido=".$id)->fetchAll(); 

            $datosbitacora = $conn->query("SELECT DISTINCT p.id, p.pedido,p.hora,p.comentarios,u.nombre_usuario, DATE_FORMAT(p.fecha,'%d/%m/%Y') as fecha FROM bitacora_pedido p, usuario u where p.usuario=u.id and p.pedido =".$id." order by p.id desc")->fetchAll(); 



           // $totalpago= $conn->query("SELECT sum(p.montopago) as total from detalles_pago p where p.pedido=".$id." and   operacion='pago'")->fetchAll(); 
           // $totalcargo= $conn->query("SELECT sum(p.montopago) as totalcargo from detalles_pago p where p.pedido=".$id." and   operacion='cargo'")->fetchAll(); 
           //  $montopago=$totalpago[0]['total'];
           //  $montopd = $conn->query("SELECT montopedido FROM montospedidos where pedido=".$id." limit 1")->fetchAll(); 
           //  $totalmt= $montopd[0]['montopedido'] + $totalcargo[0]['totalcargo'];
           //  $totalrest= $totalmt-$montopago;



            $datosrecoleccion = $conn->query("SELECT * FROM recolecciones where pedidos=$id")->fetchAll(); 
           

            if(empty($datosequipos)){

              $datosequipos = $conn->query("SELECT p.pedido,p.montototalpago,p.por_pagar, p.folio,p.devolucion,p.cliente,p.cuenta,p.fecha,s.status,p.cant,p.clave,p.equipo,p.dias,p.PU,p.importe,p.direccion_entrega,p.comentarios,p.descuento,p.impuesto,p.total,p.subtotal,p.subtotal2,p.servicioentrega,p.id FROM pedidos_archivados p,status_entrega s where p.status_pedido=s.id and  p.pedido=".$id)->fetchAll(); 
            }
    return $this->render('pedidos/detallespedido.html.twig',array('datosequipos' => $datosequipos,'recolecciones' => $datosrecoleccion,'id' => $id,'cot' => '00','datosbitacora' => $datosbitacora));
    }if($id==0){ 
       $generado='1';
        $cont=0;
        $pedido=0;
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        

        


     //   $cadena4 =$conn->query("INSERT INTO ultpedido (cliente,obra) VALUES ('cliente','obra')");
       // $pd= $conn->query("SELECT pedido FROM ultpedido order by pedido desc limit 1")->fetchAll();
        //$xx=$pd[0]['pedido'];   
        $datosequipos=array();
        $query2=array();
        $dts=array();
        $datosbitacora=array();
        
        //return new JsonResponse($pd); 
        return $this->render('pedidos/detallespedido.html.twig',array('datosequipos' => $datosequipos,'query2' => $query2,'dts' => $dts,'id' => $id,'cot'=>'00','montopagado' => '0','restante' => '0','datosbitacora' => '0'));
        
        }
    }  




    public function modificarpedidoAction(Request $request){
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
        $cot = ($_POST['cot']);
        $cantidad = ($_POST['cantidad']);
        $equipo_2 = ($_POST['equipo_2']);
        $clv = ($_POST['clv']);
        $PU_1 = ($_POST['PU_1']);
        $importe = ($_POST['importe']);
        $cliente = ($_POST['cliente']);
        $cuenta = ($_POST['cuenta']);
        $fecha_1 = ($_POST['fecha_1']);
        $fecha_2 = ($_POST['fecha_2']);
        $status = ($_POST['status']);
        $pedido_1 = ($_POST['pedido_1']);
        $folio_1 = ($_POST['folio_1']);
        $subtotal_1 = ($_POST['subtotal_1']);
        $subtotal_2 = ($_POST['subtotal_2']);
        $descuento = ($_POST['descuento_1']);
        $dias = ($_POST['dias_1']);
        $impuesto = ($_POST['impuesto']);
        $total = ($_POST['total']);
        $comentario_1 = ($_POST['comentario_1']);
        $direccion_entrega = ($_POST['direccion_entrega']);
        $sv = ($_POST['sv']);
        

        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        if($cot<>0){
          $datosequiposinsr = $conn->query("UPDATE cotizaciones SET cliente='$cliente',obra='$cuenta',fecha='$fecha_1',devolucion='$fecha_2',cantidad=$cantidad,clave='$clv',equipo='$equipo_2',dias=$dias,PU=$PU_1,importe=$importe,direccion_entrega='$direccion_entrega',comentarios='$comentario_1',descuento=$descuento,impuesto=$impuesto,total=$total,subtotal=$subtotal_1,subtotal2=$subtotal_2,servicioentrega=$sv WHERE cotizacion=$cot and clave='$clv'"); 
        }else{
        $idstatus= $conn->query("SELECT id FROM status_entrega  where status='En Renta' ")->fetchAll();
        $idst= $idstatus[0]['id'];

        $idact= $conn->query("SELECT id FROM pedidos  where clave='$clv' and pedido=$pedido_1")->fetchAll();
        if (count($idact)== 0){



          $insertar = $conn->query("INSERT INTO pedidos (pedido,folio,cliente,cuenta,fecha,devolucion,status_pedido,cant,clave,equipo,dias,PU,importe,direccion_entrega,comentarios,descuento,impuesto,total,subtotal,subtotal2,servicioentrega) VALUES ($pedido_1,'$folio_1','$cliente','$cuenta','$fecha_1','$fecha_2',$idst,$cantidad,'$clv','$equipo_2',$dias,$PU_1,$importe,'$direccion_entrega','$comentario_1',$descuento,$impuesto,$total,$subtotal_1,$subtotal_2,$sv)"); 

          




          }else{
          $datosequiposinsr = $conn->query("UPDATE pedidos SET pedido=$pedido_1,folio='$folio_1',cliente='$cliente',cuenta='$cuenta',fecha='$fecha_1',devolucion='$fecha_2',status_pedido=$idst,cant=$cantidad,clave='$clv',equipo='$equipo_2',dias=$dias,PU=$PU_1,importe=$importe,direccion_entrega='$direccion_entrega',comentarios='$comentario_1',descuento=$descuento,impuesto=$impuesto,total=$total,subtotal=$subtotal_1,subtotal2=$subtotal_2,servicioentrega=$sv WHERE pedido=$pedido_1 and clave='$clv'"); 
          }
          $saldors= $conn->query("SELECT saldorestante FROM montospedidos  where pedido=$pedido_1")->fetchAll();
          $totalpago= $conn->query("SELECT sum(p.montopago) as total from detalles_pago p where p.pedido=".$pedido_1." and operacion='pago'")->fetchAll();

          $totalrest= $saldors[0]['saldorestante'];
          $tsald=($total - $totalpago[0]['total']);
          $t= $tsald + $totalrest;

          $cadenaact = $conn->query("UPDATE montospedidos SET montopedido=$total,statussaldo=1,saldorestante=$tsald WHERE pedido=$pedido_1"); 
          
          //$montopd = $conn->query("SELECT montopedido FROM montospedidos where pedido=$pedido_1 limit 1")->fetchAll();    
          $totalcargo= $conn->query("SELECT sum(p.montopago) as total from detalles_pago p where p.pedido=$pedido_1 and p.operacion='cargo'")->fetchAll(); 
          $totalpago= $conn->query("SELECT sum(p.montopago) as totalpago from detalles_pago p where p.pedido=$pedido_1 and p.operacion='pago'")->fetchAll(); 


          $totalmt= round($totalcargo[0]['total'],2) + round($total,2);
          $porpagar= $totalmt - round($totalpago[0]['totalpago'],2);

          $cadenaact = $conn->query("UPDATE pedidos SET montototalpedido=$totalmt,por_pagar=$porpagar WHERE pedido=$pedido_1"); 

           }
        
    }

    public function buscarclaveAction(Request $request)
    {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
            $generardatos = array();
            $consultaBusqueda = $_POST['valorBusqueda'];
            $manager = $this->getDoctrine()->getManager();
            $conn = $manager->getConnection();
                    $em = $this->getDoctrine()->getManager();
                    $queryc = $em->createQuery(
                    'SELECT p
                    FROM GestionBundle:Catalogo p
                    WHERE p.clave = :consultaBusqueda'
                    )->setParameter('consultaBusqueda', $consultaBusqueda)
                     ->setMaxResults(1);
                    $query2 = $queryc->getResult();
                    if(count($query2)>0){
                    foreach($query2 as $entity){
                            $equipo2= $entity->getEquipo();
                            $precio= $entity->getPrecio();
                            $status= $entity->getStatus();
                            $localidad['equipo2']  = $equipo2;
                            $localidad['precio']  = $precio;
                            $localidad['status']  = $status;
                            $generardatos[] = $localidad;
                     }
                }
                     if(count($query2)==0){
                        $localidad['equipo2']  = 'N';
                        $generardatos[] = $localidad;

                     }
                     return new JsonResponse($generardatos); 
    }
    public function consultarrecoleccionAction(Request $request){

        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }

        $pedido=$_POST['pedido'];
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $fecha= $conn->query("SELECT fecha_recoleccion FROM pedidos where pedido=".$pedido."")->fetchAll();
          $añod = substr($fecha[0]['fecha_recoleccion'],0,4);
          $diad = substr($fecha[0]['fecha_recoleccion'],8,2);
          $mesd = substr($fecha[0]['fecha_recoleccion'],5,2);
          $fecharec=$diad."-".$mesd."-".$añod;

        return new JsonResponse($fecharec); 
    }
    public function busquedarapidaAction(Request $request){

    $session = $request->getSession(); 
            if(!$session->get("usuarionombre")){
                $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
                return $this->redirect($this->generateUrl('usuario_login'));
            }
            $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
            $clientes = $conn->query("SELECT c.razon_social,ct.domicilio,ct.cuenta_id FROM cuentas_cliente ct,clientes c where ct.cliente=c.cliente and c.desactivar=0")->fetchAll(); 
            return $this->render('pedidos/busquedarapida.html.twig', array(
            'clientes'=> $clientes
          ));
        

    }
     public function generarbusqrapidaAction(Request $request){

    $session = $request->getSession(); 
      if(!$session->get("usuarionombre")){
          $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
          return $this->redirect($this->generateUrl('usuario_login'));
      }
      $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();


      $cliente=$_POST['cliente'];
      $folio=$_POST['folio'];
      $pedido=$_POST['pedido'];
      $desde=$_POST['fecha'];

      // $cliente='';
      // $folio='';
      // $pedido='';
      // $desde='20/11/2018';

      // $idcliente= $conn->query("SELECT cliente FROM clientes where razon_social='$cliente' ")->fetchAll();
      // $id= $idcliente[0]['cliente'];
      //$montopd = $conn->query("SELECT montopedido,saldorestante FROM montospedidos where pedido=".$pedido)->fetchAll(); 
    //   $sql="SELECT DISTINCT (SELECT sum(dt.montopago) FROM detalles_pago dt where dt.operacion='pago' and dt.pedido=p.pedido)as total , p.pedido as Pedido,p.folio as Folio,p.cliente as Cliente,p.cuenta as Obra,c.telefono,m.saldorestante FROM pedidos p, clientes c,montospedidos m, detalles_pago dt where c.razon_social=p.cliente and m.pedido=p.pedido and p.pedido=m.pedido";

//       $sql="SELECT DISTINCT p.pedido as Pedido,p.folio as Folio,p.cliente as Cliente,p.cuenta as Obra,c.telefono,m.saldorestante FROM pedidos p, clientes c,montospedidos m, detalles_pago dt where c.razon_social=p.cliente and m.pedido=p.pedido and p.pedido=m.pedido";
      $sql="SELECT DISTINCT p.pedido as Pedido,p.folio as Folio,p.cliente as Cliente,p.cuenta as Obra,c.telefono,p.montototalpago,p.por_pagar FROM pedidos p, clientes c where c.razon_social=p.cliente";

        // $totalpago= $conn->query("SELECT sum(p.montopago) as total from detalles_pago p where p.pedido=".$id." and   operacion='pago'")->fetchAll(); 
        //     $montopago=$totalpago[0]['total'];
        //     $montopd = $conn->query("SELECT montopedido FROM montospedidos where pedido=".$id." limit 1")->fetchAll(); 

       // $añod = substr($desde,6,4);
       // $diad = substr($desde,0,2);
       // $mesd = substr($desde,3,2);
       // $fecha_rechasta1=$añod."-".$mesd."-".$diad;
       if ($cliente<>'')
        {
          $sql= $sql." and p.cliente='".$cliente."'";
        }
        if ($folio<>''){
            $sql= $sql." and p.folio='".$folio."'";
        }

        if ($pedido<>''){
            $sql= $sql." and p.pedido='".$pedido."'";
        }
        if ($desde <> ''){
              $sql= $sql." and p.devolucion='".$desde."'";
        }

        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $dts= $conn->query($sql)->fetchAll();
        // var_dump($dts);
        // exit();
        return new JsonResponse($dts);
          
      

    }

       public function generarbusqavanzadaAction(Request $request){

          $session = $request->getSession(); 
          if(!$session->get("usuarionombre")){
              $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
              return $this->redirect($this->generateUrl('usuario_login'));
          }
          $manager = $this->getDoctrine()->getManager();
          $conn = $manager->getConnection();


          $cliente=$_POST['cliente'];
          $folio=$_POST['folio'];
          $pedido=$_POST['pedido'];
          $fecha1=$_POST['fecha'];
          $desde=$_POST['desde'];
          $hasta=$_POST['hasta'];
          $statusdev=$_POST['statusdev'];
          $statuspago=$_POST['statuspago'];

          // $cliente='';
          // $folio='';
          // $pedido='296';
          // $desde='';
          // $desde='';
          // $hasta='';
          // $statusdev='';
          // $statuspago='';

          $sql="SELECT DISTINCT p.pedido as Pedido,p.folio as Folio,p.cliente as Cliente,p.cuenta as Obra,c.telefono,p.fecha,p.devolucion,p.por_pagar,s.status, ss.statussaldos,p.montototalpago FROM status_entrega s,pedidos p, clientes c,montospedidos mt , status_saldos ss where c.razon_social=p.cliente and s.id=p.status_pedido and mt.pedido=p.pedido and mt.statussaldo=ss.id";


              $añod = substr($hasta,6,4);
              $diad = substr($hasta,0,2);
              $mesd = substr($hasta,3,2);
              $fechahasta=$añod."-".$mesd."-".$diad;

              $añod2 = substr($desde,6,4);
              $diad2 = substr($desde,0,2);
              $mesd2 = substr($desde,3,2);
              $fechadesde=$añod2."-".$mesd2."-".$diad2;

           if ($cliente<>'')
            {
              $sql= $sql." and p.cliente='".$cliente."'";
            }
            if ($folio<>''){
                $sql= $sql." and p.folio='".$folio."'";
            }

            if ($pedido<>''){
                $sql= $sql." and p.pedido='".$pedido."'";
            }
            if ($statusdev<>''){
                $sql= $sql." and p.status_pedido='".$statusdev."'";
            }
            if ($statuspago<>''){
                $sql= $sql." and mt.statussaldo='".$statuspago."'";
            }
            if ($desde <> ''){
                  $sql= $sql." and p.devolucion='".$desde."'";
            }
            if ($fecha1 <> ''){
                  $sql= $sql." and p.fecha='".$fecha1."'";
            }
          if ($fechahasta <> '--' and $fechadesde <> '--'){
                $sql= $sql." and p.fecha >= '".$fechadesde."' and p.fecha <= '".$fechahasta."'";
          }

            $manager = $this->getDoctrine()->getManager();
            $conn = $manager->getConnection();
            $dts= $conn->query($sql)->fetchAll();
              // var_dump($dts);
              // exit();
            return new JsonResponse($dts);
          
      

    }

     public function filtrogeneralrecoleccionAction(Request $request){

        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }

      
      $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
       $sql="SELECT DISTINCT p.pedido as Pedido,p.folio as Folio,p.cliente as Cliente,p.cuenta as Obra,p.fecha as Creado,p.devolucion as Devolucion, DATE_FORMAT(p.fecha_recoleccion,'%d/%m/%Y') as Fecha_Recoleccion FROM pedidos p";
          
      $cliente=$_POST['cliente'];
      $folio=$_POST['folio'];
      $pedido=$_POST['pedido'];
      $desde=$_POST['fecha'];
      $hasta=$_POST['hasta'];
      $fecha_rechasta=$_POST['fecha_recoleccionhasta'];
      $fecha_recdesde=$_POST['fecha_recolecciondesde'];


      //  $cliente='';
      // $folio='';
      // $pedido='1246';
      // $desde='';
      // $hasta='';
      // $fecha_rechasta='';
      // $fecha_recdesde='';

 //     if ($fecha_rechasta<>''){
      $añod = substr($fecha_rechasta,6,4);
      $diad = substr($fecha_rechasta,0,2);
      $mesd = substr($fecha_rechasta,3,2);
      $fecha_rechasta1=$añod."-".$mesd."-".$diad;
   //   }
      //if ($fecha_recdesde<>''){
      $añod2 = substr($fecha_recdesde,6,4);
      $diad2 = substr($fecha_recdesde,0,2);
      $mesd2 = substr($fecha_recdesde,3,2);
      $fecha_recdesde2=$añod2."-".$mesd2."-".$diad2;
    //}
    $con=0;
       if ($cliente<>'')
        {
          $sql= $sql." where p.cliente like '".$cliente."%'";
          $con=1;
        }
        if ($folio<>''){
          if($con==1){
            $sql= $sql." and p.folio like '".$folio."%'";
          }else{
            $sql= $sql." where p.folio like '".$folio."%'";
          }
          $con=2;
        }

        if ($pedido<>''){
          if ($con==2 or $con==1){
            $sql= $sql." and p.pedido like '".$pedido."%'";
          }else{
            $sql= $sql." where p.pedido like '".$pedido."%'";
          }
          $con=3;
        }
        if ($desde <> '' and $hasta <> ''){
          if ($con==2 or $con=1 or $con==3){
              $sql= $sql." and p.fecha >= '".$desde."' and p.fecha <= '".$hasta."'";
            }else{
              $sql= $sql." where p.fecha >= '".$desde."' and p.fecha <= '".$hasta."'";

            }
            $con=4;
        }
         if ($desde <> '' and $hasta == '') {
          if ($con==2 or $con=1 or $con==3 or $con==4){
            $sql= $sql." and p.fecha = '".$desde."'";
          }else{
            $sql= $sql." where p.fecha = '".$desde."'";
          }
          $con=5;
        }
        if ($fecha_recdesde2 <> '--' and $fecha_rechasta1 <> '--'){
          if ($con<>0){
              $sql= $sql." and p.fecha_recoleccion >= '".$fecha_recdesde2."' and p.fecha_recoleccion <= '".$fecha_rechasta1."'";
          }else{
            $sql= $sql." where p.fecha_recoleccion >= '".$fecha_recdesde2."' and p.fecha_recoleccion <= '".$fecha_rechasta1."'";
          }
          $con=6;
        }
         if ($fecha_recdesde2 <> '--' and $fecha_rechasta1 == '--') {
          if ($con<>0){
            $sql= $sql." and p.fecha_recoleccion = '".$fecha_recdesde2."'";
          }else{
            $sql= $sql." where p.fecha_recoleccion = '".$fecha_recdesde2."'";
          }
        }
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $dts= $conn->query($sql)->fetchAll();
        return new JsonResponse($dts); 
    }
  public function buscarequiposAction(Request $request)
    {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
            $generardatos = array();
            $consultaBusqueda = $_POST['valorBusqueda'];
            $manager = $this->getDoctrine()->getManager();
            $conn = $manager->getConnection();
            $em = $this->getDoctrine()->getManager();
            $queryc = $em->createQuery(
                    'SELECT p
                    FROM GestionBundle:Catalogo p
                    WHERE p.equipo = :consultaBusqueda'
                    )->setParameter('consultaBusqueda', $consultaBusqueda)
                     ->setMaxResults(1);
                    $query2 = $queryc->getResult();
                    if(count($query2)>0){
                    foreach($query2 as $entity){
                            $clave= $entity->getClave();
                            $precio= $entity->getPrecio();
                            $status= $entity->getStatus();
                            $localidad['clave']  = $clave;
                            $localidad['precio'] = $precio;
                            $localidad['status'] = $status;
                            $generardatos[] = $localidad;
                     }
                }  if(count($query2)==0){
                        $localidad['clave']  = 'N';
                        $generardatos[] = $localidad;

                     }
               
                    return new JsonResponse($generardatos); 
    }

    public function registrofolioAction(Request $request)
    {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
        $folio=$_POST['folio'];
        $fecha_1=$_POST['fechaactfl'];
        $pedido=$_POST['pedido'];
        $status_entrega=$_POST['status_entrega'];
        $clave=$_POST['clave'];
        $equipo=$_POST['equipo'];
        $cantidad=$_POST['cantidad'];
        $total=$_POST['total'];
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        // $fecha=date("m/d/Y");
         $em = $this->getDoctrine()->getManager();
         // $queryc = $em->createQuery(
         //    'SELECT p
         //    FROM GestionBundle:Pedidos p
         //    WHERE p.folio = :consultaBusqueda'
         //    )->setParameter('consultaBusqueda', $folio)
         //     ->setMaxResults(1);
         //    $query2 = $queryc->getResult();
            // if(count($query2)==0){
            //     if ($fecha_1==$fecha){
                
            //     }else{
            //     //$cadenafolio = $conn->query("UPDATE pedidos SET folio='$folio', status_pedido=1 WHERE pedido=$pedido");
            //     }
            // }

            $cadenaP = $conn->query("UPDATE pedidos SET folio='$folio' WHERE pedido=$pedido"); 
         $identrega= $conn->query("SELECT id FROM status_entrega where status='$status_entrega' ")->fetchAll();
          $id= $identrega[0]['id'];

          $idpden= $conn->query("SELECT * FROM pedidos_entregados where pedido=$pedido ")->fetchAll();
        if (count($idpden)== 0){
             $cadena2 =$conn->query("INSERT INTO pedidos_entregados (foliofisico,fechafolio,statuspedido,pedido) VALUES ($folio,'$fecha_1',$id,$pedido)");
        }
            
             $cadena3 =$conn->query("INSERT INTO detalles_devoluciones (pedidosistema,foliopadre,foliodevolucion,fechamovimiento,cantidad,claveequipo,equipo) VALUES ($pedido,$folio,0,'$fecha_1',$cantidad,'$clave','$equipo')");
         if($total<>0){
            $status_saldo=1;
         }else{
            $status_saldo=2;
         }
    }

    // public function actpagoAction(Request $request)
    // {
    //     $session = $request->getSession(); 
    //     if(!$session->get("usuarionombre")){
    //         $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
    //         return $this->redirect($this->generateUrl('usuario_login'));

    //     }
    //     $pedido=$_POST['pedido'];
    //     $total=$_POST['total'];
    //     $statusf=$_POST['statusf'];
    //     $cr_ped=$_POST['cr_ped'];
    //      $manager = $this->getDoctrine()->getManager();
    //     $conn = $manager->getConnection();
    //     //$cadena = $conn->query("UPDATE factura SET saldo=$total,status_financiero='$statusf',cargo_pedido=$cr_ped WHERE pedido=$pedido");
    //      $cadena2 = $conn->query("UPDATE pedidos SET montoact=$total,total=$cr_ped WHERE pedido=$pedido");
    //     if($statusf=='Sin Adeudo'){
    //         $cadena = $conn->query("UPDATE pedidos SET status_pago='Sin Adeudo',montoact=$total,total=$cr_ped WHERE pedido=$pedido");
    //     }

    // }


   

 public function consultaspdsAction(Request $request)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $status= $conn->query("SELECT * FROM status_saldos")->fetchAll();    
          return $this->render('pedidos/consultaspedidos.html.twig', array(
            'status'=> $status
          ));
      }
      public function configuracionAction(Request $request)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        return $this->render('usuario/configuracion.html.twig');
      }
      
      public function consultdevdetallesAction(Request $request)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        return $this->render('pedidos/consultasdevoluciones.html.twig');
      }
      public function estadopedidoAction(Request $request)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }

        $pedido =  ($_POST['pedido']);
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $pas=0;
        $estadoped= $conn->query("SELECT status_pedido FROM pedidos where pedido=".$pedido."")->fetchAll();
        $estadoped=$estadoped[0]['status_pedido'];

        $estadopago= $conn->query("SELECT statussaldo FROM montospedidos where pedido=".$pedido."")->fetchAll();
        $estadopago=$estadopago[0]['statussaldo'];

        if($estadopago=='2' and $estadoped=='2'){
          return new JsonResponse('Completado');
        }else{
          return new JsonResponse('Pendiente');
        }
        
      }
      

      // public function rptpedidoAction(Request $request)
      // {
      //   $session = $request->getSession(); 
      //   if(!$session->get("usuarionombre")){
      //       $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
      //       return $this->redirect($this->generateUrl('usuario_login'));

      //   }
      //   return $this->render('reportes/reportespedidos.html.twig');
      // }

public function llenardtAction(Request $request)
  {
    $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('GestionBundle:Clientes')->findAll();
                foreach($entities as $entity){
                            $clave= $entity->getRazonSocial();                            
                            $localidad['razonsocial']  = $clave;                            
                            $generardatos[] = $localidad;
                     }
                    return new JsonResponse($generardatos); 
  }

  public function llenarctaAction(Request $request)
  {
    $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        $cliente= $_POST['clienterz'];// 'Arq Nayeli';  //
        $restr='';
        $generardatos = array();
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $em = $this->getDoctrine()->getManager();
        $queryc = $em->createQuery(
        'SELECT p
        FROM GestionBundle:Clientes p
        WHERE p.razonSocial = :consultaBusqueda'
        )->setParameter('consultaBusqueda', $cliente)
        ->setMaxResults(1);
        $query2 = $queryc->getResult();
        $clienteid =  0;
        foreach($query2 as $entity){
          $clienteid= $entity->getCliente();
          $restr= $entity->getRestringir();
          $localidad['restringir']  = $restr;

        }

       
        $queryc = $em->createQuery(
        'SELECT p
        FROM GestionBundle:CuentasCliente p
        WHERE p.cliente = :consultaBusqueda'
        )->setParameter('consultaBusqueda', $clienteid);
        $query2 = $queryc->getResult();
     //   $generardatos = array();
        foreach($query2 as $entity){
            $cuenta= $entity->getCuentaId();
            $localidad['cuenta_Id']  = $cuenta;
            
            $generardatos[] = $localidad;
        }
        //  var_dump($generardatos);
         // exit();
       
        return new JsonResponse($generardatos);
  }
  public function eliminarCotAction(Request $request)
  {
    $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
        $cotizacion = ($_POST['cotizaciones']);
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $eliminar= $conn->query("DELETE FROM cotizaciones where cotizacion=$cotizacion");    
        return new JsonResponse('Cotización Eliminada');

  }

   public function validacionAction(Request $request){
    $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        
        //unset($session->get("IdPedido"));
      $cliente = ($_POST['cliente']);
      $cuenta = ($_POST['cuenta']);
      $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
      $pas=0;
         $vfcliente= $conn->query("SELECT * FROM clientes where razon_social='".$cliente ."'")->fetchAll();
          if (count($vfcliente)== 0){
            $pas=1;
          }  

         $vfcta= $conn->query("SELECT * FROM cuentas_cliente where cuenta_id='".$cuenta."'")->fetchAll();
          if (count($vfcta)== 0){
            $pas=2;
          }  
        return new JsonResponse($pas);


 }
   public function registrarpedidoAction(Request $request)
  {
    $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
    $cantidad = ($_POST['cantidad']);
    $dias = ($_POST['dias_1']);
    $equipo_2 = ($_POST['equipo_2']);
    $clv = ($_POST['clv']);
    $PU_1 = ($_POST['PU_1']);
    $importe = ($_POST['importe']);
    $cliente = ($_POST['cliente']);
    $cuenta = ($_POST['cuenta']);
    $fecha_1 = ($_POST['fecha_1']);
    $fecha_2 = ($_POST['fecha_2']);
    $status = ($_POST['status']);
    $pedido_1 = ($_POST['pedido_1']);
    $folio_1 = ($_POST['folio_1']);
    $subtotal_1 = ($_POST['subtotal_1']);
    $subtotal_2 = ($_POST['subtotal_2']);
    $descuento = ($_POST['descuento_1']);
    $impuesto = ($_POST['impuesto']);
    $total = ($_POST['total']);
    $comentario_1 = ($_POST['comentario_1']);
    $f = ($_POST['f']);
    $radio = ($_POST['radio']);
    $direccion_entrega = ($_POST['direccion_entrega']);
    $sventrega = ($_POST['sventr']);
    $cotz = ($_POST['cot']);

    $manager = $this->getDoctrine()->getManager();
    $conn = $manager->getConnection();

          $añod = substr($fecha_1,6,4);
          $diad = substr($fecha_1,0,2);
          $mesd = substr($fecha_1,3,2);
          $fechafiltrodesde=$añod."-".$mesd."-".$diad;

             

    if($radio=='Pedido'){
        if($cotz<>''){
          $idct= $conn->query("SELECT id FROM cotizaciones where cotizacion=$cotz ")->fetchAll();
          //$idfiltro= $idct[0]['id'];
          if (count($idct)<> 0){
            $conn->query("UPDATE cotizaciones SET status='En Renta',pedido=$pedido_1 where cotizacion=$cotz");
          }     
        }
        $conn->query("UPDATE ultpedido SET cliente='$cliente',obra='$cuenta' where pedido=$pedido_1");
        $idsta= $conn->query("SELECT id FROM status_entrega where status='$status' ")->fetchAll();
        $idstat= $idsta[0]['id'];
        // $ultimopedidos= $conn->query("SELECT pedido FROM pedidos  order by id desc limit 1")->fetchAll();
        //  if (count($ultimopedidos)== 0){
        //     $pdult=1;    
        //  }else{
        //     $pdult= $ultimopedidos[0]['pedido'];    
        //  }
         //$idact= $conn->query("SELECT pedido FROM ultpedido  where pedido=$pedido_1")->fetchAll();
          //if (count($idact)==0){
//            $cadena4 =$conn->query("INSERT IGNORE INTO ultpedido (pedido,cliente,obra) VALUES ($pedido_1,'$cliente','$cuenta')");
          //}
        //  $idact= $conn->query("SELECT * FROM montospedidos  where pedido=$pedido_1")->fetchAll();
         // if (count($idact)== 0){
            $cadena4 =$conn->query("INSERT IGNORE INTO montospedidos (montopedido,statussaldo,pedido,saldorestante,fecha) VALUES ($total,1,$pedido_1,$total,'$fechafiltrodesde')");
          //}
         $ultpedido1= $conn->query("SELECT id FROM montospedidos  order by id desc limit 1")->fetchAll();
         if (count($ultpedido1)== 0){
           $ultp= 1;
           $id2= 1;
         }else{
           $id2= $ultpedido1[0]['id'];
         }



 // $ultpds= $conn->query("SELECT pedido FROM ultpedido  order by pedido desc limit 1")->fetchAll();
 //         if (count($ultpds)== 0){
 //          $ulp= 1;
 //         }else{
 //          $ulp= $ultpds[0]['pedido']+1;
 //         }

        $cadenaP = $conn->query( "INSERT INTO pedidos (pedido,folio,cliente,cuenta,fecha,devolucion,status_pedido,cant,clave,equipo,dias,PU,importe,direccion_entrega,comentarios,descuento,impuesto,total,subtotal,subtotal2,servicioentrega,idmontopedido,fecharg,montototalpedido,por_pagar) VALUES ($pedido_1, '$folio_1', '$cliente','$cuenta','$fecha_1','$fecha_2',$idstat,'$cantidad','$clv','$equipo_2','$dias','$PU_1','$importe','$direccion_entrega','$comentario_1','$descuento','$impuesto','$total','$subtotal_1',$subtotal_2,$sventrega,$id2,'$f',$total,$total)");
        //return new JsonResponse($ultpedido1);
        
        //$session = $request->getSession();
        $session->remove('IdPedido');
    }else{
      $cadenaP = $conn->query( "INSERT INTO cotizaciones (cliente,obra,fecha,devolucion,status,cantidad,clave,equipo,dias,PU,importe,direccion_entrega,comentarios,descuento,impuesto,total,subtotal,subtotal2,servicioentrega,fechaHoy,cotizacion) VALUES ('$cliente','$cuenta','$fecha_1','$fecha_2','Cotizacion','$cantidad','$clv','$equipo_2','$dias','$PU_1','$importe','$direccion_entrega','$comentario_1','$descuento','$impuesto','$total','$subtotal_1',$subtotal_2,$sventrega,'$f',$cotz)");
  $ultpedido1= $conn->query("SELECT cotizacion FROM cotizaciones  order by cotizacion desc limit 1")->fetchAll();
   return new JsonResponse($ultpedido1);
    }
    
  }
  public function ultipedidoAction(Request $request)
  {

    //$radio = ($_POST['radio']);
    //$cliente = ($_POST['cliente']);
    //$obra = ($_POST['obra']);
    $manager = $this->getDoctrine()->getManager();
    $conn = $manager->getConnection();
    //if($radio=='Pedido'){
    


     //     $session = $request->getSession(); 
     // if ($session->get("IdPedido")==''){
       $conn->query("INSERT INTO ultpedido (cliente,obra) VALUES ('cliente','obra')");  
       $pdconsulta= $conn->query("SELECT * FROM ultpedido  order by pedido desc limit 1")->fetchAll();
       //$querydoctrine = ("");
      //$idttomand = $pdconsulta->getId();
     //     $ultpds= $conn->query()->fetchAll();
     //     $session->set("IdPedido",$ultpds[0]['pedido']);   


     // }
    
         //exit();
         // if (count($ultpds)== 0){
         //  $ulp= 1;
         // }else{
          // $ultpds= $ultpds[0]['pedido'];
         // }
    //}else{
        // $ultpds= $conn->query("SELECT cotizacion FROM cotizaciones  order by cotizacion desc limit 1")->fetchAll();
        //  if (count($ultpds)== 0){
        //   $ultpds= 1;
        //  }else{
        //   $ultpds= $ultpds[0]['cotizacion']+1;
        //  }
    //}
           

         return new JsonResponse($pdconsulta[0]['pedido']);
  }
  public function consultarcotAction(Request $request)
  {
     $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
      $sql="SELECT DISTINCT p.cotizacion,p.cliente,p.obra,p.pedido,p.fecha,p.status FROM cotizaciones p";
      $con=0;
      $cliente=$_POST['cliente'];
      $obra=$_POST['obra'];
      $desde=$_POST['desde'];
      $hasta=$_POST['hasta'];
      $cotizacion=$_POST['cotizacion'];


      if ($cliente<>'')
        {
          $sql= $sql." where cliente like '".$cliente."%'";
          $con=1;
        }
        if ($cotizacion<>''){
          if($con==1){
            $sql= $sql." and cotizacion like '".$cotizacion."%'";
          }else{
            $sql= $sql." where cotizacion like '".$cotizacion."%'";
          }
          $con=2;
        }
        if ($desde <> '' and $hasta <> ''){
            if($con==1 or $con==2){
              $sql= $sql." and fecha >= '".$desde."' and fecha <= '".$hasta."'";
          }else{
            $sql= $sql." where fecha >= '".$desde."' and fecha <= '".$hasta."'";
          }
          $con=3;
        }
         if ($desde <> '' and $hasta == '') {
          if($con==1 or $con==2 or $con==3){
            $sql= $sql." and fecha = '".$desde."'";
          }else{
            $sql= $sql." where fecha = '".$desde."'";
          }
          $con=4;
        }
        if ($obra <> '' ){
          if($con==1 or $con==2 or $con==3 or $con==4){
              $sql= $sql." and obra like '".$obra."%'";
          }else{
            $sql= $sql." where obra like '".$obra."%'";
          }
        }
         $manager = $this->getDoctrine()->getManager();
         $conn = $manager->getConnection();
         $dts= $conn->query($sql)->fetchAll();
         return new JsonResponse($dts); 
  }
  public function consultaspdstablaAction(Request $request)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }

      $sql="SELECT DISTINCT p.cliente,p.cuenta,p.folio,p.pedido,p.fecha,p.devolucion,s.status, p.idmontopedido,  sal.statussaldos FROM pedidos p, status_entrega s, status_saldos sal, montospedidos m  where p.status_pedido=s.id and p.idmontopedido=m.id and m.statussaldo=sal.id";

      //"SELECT DISTINCT p.cliente,p.cuenta,p.folio,p.pedido,p.fecha,p.devolucion,s.status,sal.statussaldos FROM pedidos p, status_entrega s, status_saldos sal  where p.status_pedido=s.id";
      $con=0;
      $cliente=$_POST['cliente'];
      $folio=$_POST['folio'];
      $pedido=$_POST['pedido'];
      $desde=$_POST['desde'];
      $hasta=$_POST['hasta'];
      $desdedev=$_POST['desdedev'];
      $hastadev=$_POST['hastadev'];
 //     $status=$_POST['status'];

      $añod1 = substr($desde,6,4);
      $diad1 = substr($desde,0,2);
      $mesd1 = substr($desde,3,2);
      $desde2=$añod1."-".$mesd1."-".$diad1;

      $año2 = substr($hasta,6,4);
      $dia2 = substr($hasta,0,2);
      $mes2 = substr($hasta,3,2);
      $hasta2=$año2."-".$mes2."-".$dia2;

      $añodev = substr($desdedev,6,4);
      $diadev = substr($desdedev,0,2);
      $mesdev = substr($desdedev,3,2);
      $desdedev1=$añodev."-".$mesdev."-".$diadev;

      $añodev2 = substr($hastadev,6,4);
      $diadev2 = substr($hastadev,0,2);
      $mesdev2 = substr($hastadev,3,2);
      $hastadev1=$añodev2."-".$mesdev2."-".$diadev2;

     
    if ($desdedev1 <> '--' and $hastadev1 <> '--'){
      $sql= $sql." and  str_to_date(p.devolucion, '%d/%m/%Y') between cast('".$desdedev1."' as date) and cast('".$hastadev1."' as date)";
    }
      if ($cliente<>'')
        {
          $sql= $sql." and p.cliente like '".$cliente."%'";
        }
        if ($folio<>'')
        {
            $sql= $sql." and p.folio like '".$folio."%'";
        }
        if ($pedido<>''){
            $sql= $sql." and p.pedido like '".$pedido."%'";
        }
        if ($desde2 <> '--' and $hasta2 <> '--'){
          $sql= $sql." and  str_to_date(p.fecha, '%d/%m/%Y') between cast('".$desde2."' as date) and cast('".$hasta2."' as date)";
        }
        if ($desde2 <> '' and $hasta2 == '') {
            $sql= $sql." and p.fecha = '".$desde."'";
        }
        
        if ($desdedev <> '' and $hastadev == '') { 
            $sql=$sql. " and p.devolucion = '".$desdedev."'"; 
          }
        // if ($status <> '') { 
        //   $sql=$sql. " and sal.id = '".$status."'"; 
        // }


        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $sql=$sql."order by p.pedido asc";
        $dts= $conn->query($sql)->fetchAll();
        return new JsonResponse($dts); 
      }
       public function confestadosAction(Request $request)
      {
        
       $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
      
         $manager = $this->getDoctrine()->getManager();
         $conn = $manager->getConnection();
         $em = $this->getDoctrine()->getManager();


           $pdconsulta= $conn->query(" SELECT * FROM pedidos")->fetchAll();
          
      for ($i=0;$i<count($pdconsulta); $i++)
        {
          $ped=$pdconsulta[$i]['pedido'];
          $pdconsulta2= $conn->query(" SELECT statuspedido FROM pedidos_entregados where pedido=".$ped )->fetchAll();
          if (count($pdconsulta2)== 0){
                 $conn->query("UPDATE pedidos SET status_pedido=1 where pedido=".$ped);
          }else{
              $st=$pdconsulta2[0]['statuspedido'];
               $conn->query("UPDATE pedidos SET status_pedido=$st where pedido=".$ped);
          }
        }
      }
      public function actualizarestadopdAction(Request $request)
      {
        
       $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
      $manager = $this->getDoctrine()->getManager();
         $conn = $manager->getConnection();
         $em = $this->getDoctrine()->getManager();

           $pdconsulta= $conn->query(" SELECT * FROM pedidos where status_pedido=1")->fetchAll();
            for ($i=0;$i<count($pdconsulta); $i++){
              $ped=$pdconsulta[$i]['pedido'];
              $devolucionpd=$pdconsulta[$i]['devolucion'];
              $añod = substr($devolucionpd,6,4);
              $diad = substr($devolucionpd,0,2);
              $mesd = substr($devolucionpd,3,2);
              $fechafiltrodesde=$añod."-".$mesd."-".$diad;
              $fechaactual = date("Y/m/d");

              $pdconsulta2= $conn->query(" SELECT id FROM pedidos_entregados where pedido=".$ped)->fetchAll();
              
              if (count($pdconsulta2)<> 0){
                 if ($fechaactual > $fechafiltrodesde) {
                        $conn->query("UPDATE pedidos SET status_pedido='4' where pedido=".$ped);
                  }
              }

            }





                       //   $statuspd= $entity->getStatusPedido();  
                         // $devolucionpd= $entity->getDevolucion();
                         
                          //$fechaevento = $fechafiltrodesde;
                     
                    

      }

 public function imprimircotAction($cuenta,$cliente,$desde,$hasta,$cotizacion,Request $request)
     { 
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        $sql="SELECT DISTINCT p.cotizacion,p.cliente,p.obra,p.pedido,p.fecha,p.status,p.devolucion FROM cotizaciones p";
        $con=0;
        $desde= str_replace("-", "/", $desde);
        $hasta= str_replace("-", "/", $hasta);

      if ($cliente<>'0')
        {
          $sql= $sql." where cliente like '".$cliente."%'";
          $con=1;
        }
        if ($cotizacion<>'xxx'){
          if($con==1){
            $sql= $sql." and cotizacion like '".$cotizacion."%'";
          }else{
            $sql= $sql." where cotizacion like '".$cotizacion."%'";
          }
          $con=2;
        }
        if ($desde <> '0' and $hasta <> '0'){
            if($con==1 or $con==2){
              $sql= $sql." and fecha >= '".$desde."' and fecha <= '".$hasta."'";
          }else{
            $sql= $sql." where fecha >= '".$desde."' and fecha <= '".$hasta."'";
          }
          $con=3;
        }
        if ($desde <> '0' and $hasta == '0') {
          if($con==1 or $con==2 or $con==3){
            $sql= $sql." and fecha = '".$desde."'";
          }else{
            $sql= $sql." where fecha = '".$desde."'";
          }
          $con=4;
        }
        if ($cuenta <> '0' ){
          if($con==1 or $con==2 or $con==3 or $con==4){
              $sql= $sql." and obra like '".$cuenta."%'";
          }else{
            $sql= $sql." where obra like '".$cuenta."%'";
          }
        }
        //var_dump($sql);
         $manager = $this->getDoctrine()->getManager();
         $conn = $manager->getConnection();
         $peds= $conn->query($sql);
        
        $con=0;
        
        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('JJC');
        $pdf->SetTitle(('Reporte_Pedidos'));
        $pdf->SetSubject('Our Code World Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('Helvetica', '', 10); 
        $pdf->AddPage();
        
        $content = ''; 
        $content .= ' 

        <div class=""> 
          <h3 style="color:#00BFFF">Fecha de Impresión:'.date("d-m-Y").' </h3>
        </div>
            <div class="col-md-12"> 
                <h1 style="text-align:center;">REPORTE DE COTIZACIONES</h1> 
            
                    <table border="1" cellpadding="5"> 
                      <thead> 
                        <tr align="center"> 
                         <th bgcolor="#00BFFF" style="font-size:100%;">Cotizacion</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Cliente</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Obra</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Fecha</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Status</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Devolución</th>
                        </tr> 
                      </thead> 
                      '; 

                      while($row = $peds->fetch()) {

                      $content .= ' 
                              <tr> 
                          <td style= "text-align:center;">'.$row['cotizacion'].'</td> 
                          <td style= "text-align:center;">'.$row['cliente'].'</td> 
                          <td style= "text-align:center;">'.$row['obra'].'</td> 
                          <td style= "text-align:center;">'.$row['fecha'].'</td>
                          <td style= "text-align:center;">'.$row['status'].'</td> 
                          <td style= "text-align:center;">'.$row['devolucion'].'</td> 
                      </tr> 
                      '; 
                      } 

                      $content .= '</table>'; 
     
    $content .= ' 
        <div class="row padding"> 
            <div class="col-md-12" style="text-align:center;"> 
                 
                </div> 
                </div> 
         
    '; 
     
       $pdf->writeHTML($content, true, 0, true, 0); 
       $pdf->lastPage(); 
       $pdf->output('Reporte_Cotizaciones.pdf', 'I');

     }


     public function sqlpedAction($cliente,$cuenta,$desde,$hasta,$folio,$pedido,Request $request)
     { 
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        $desde= str_replace("-", "/", $desde);
        $hasta= str_replace("-", "/", $hasta);
        $con=0;
        $sql="SELECT p.pedido,p.cliente,p.cuenta,p.fecha,s.status,p.folio,p.devolucion FROM pedidos p,status_entrega s where p.status_pedido=s.id";

         if ($cuenta<>'0')
        {
          $sql= $sql." and cuenta like '".$cuenta."%'";
        }
        if ($cliente<>'0')
        {
            $sql= $sql." and cliente like '".$cliente."%'";
        }
        if ($desde <>'0' and $hasta <>'0'){
            $sql= $sql." and fecha >= '".$hasta."' and fecha <= '".$desde."'";
        }
         if ($desde <>'0' and $hasta =='0'){ 
            $sql= $sql." and fecha = '".$desde."'";
        }
       if ($folio <>'0'){
            $sql= $sql." and folio = '".$folio."'";
       }
       if ($pedido <>'xxx'){
            $sql= $sql." and pedido = '".$pedido."'";
       }

        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $peds= $conn->query($sql);
       
        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('JJC');
        $pdf->SetTitle(('Reporte_Pedidos'));
        $pdf->SetSubject('Our Code World Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('Helvetica', '', 10); 
        $pdf->AddPage();
        
    $content = ''; 
     
    $content .= ' 

         <div class=""> 
          <h3 style="color:#00BFFF">Fecha de Impresión:'.date("d-m-Y").' </h3>
         </div>
            <div class="col-md-12"> 
                <h1 style="text-align:center;">REPORTE DE PEDIDOS</h1> 
            
                    <table border="1" cellpadding="5"> 
                      <thead> 
                        <tr align="center"> 
                         <th bgcolor="#00BFFF" style="font-size:100%;">Pedido</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Cliente</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Obra</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Fecha</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Status</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Folio</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;">Devolución</th>
                        </tr> 
                      </thead> 
                      '; 

                      while($row = $peds->fetch()) {

                      $content .= ' 
                              <tr> 
                          <td style= "text-align:center;">'.$row['pedido'].'</td> 
                          <td style= "text-align:center;">'.$row['cliente'].'</td> 
                          <td style= "text-align:center;">'.$row['cuenta'].'</td> 
                          <td style= "text-align:center;">'.$row['fecha'].'</td>
                          <td style= "text-align:center;">'.$row['status'].'</td> 
                          <td style= "text-align:center;">'.$row['folio'].'</td> 
                          <td style= "text-align:center;">'.$row['devolucion'].'</td> 
                      </tr> 
                      '; 
                      } 

                      $content .= '</table>'; 
     
    $content .= ' 
        <div class="row padding"> 
            <div class="col-md-12" style="text-align:center;"> 
                 
                </div> 
                </div> 
         
    '; 
     
       $pdf->writeHTML($content, true, 0, true, 0); 
       $pdf->lastPage(); 
       $pdf->output('Reporte_Pedidos.pdf', 'I');

     }

     public function reportepedidoAction($pedido,Request $request)
     {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }

      $sql="SELECT * from pedidos where pedido='".$pedido."'";

        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('JJC');
        $pdf->SetTitle(('Reporte_Pedidos'));
        $pdf->SetSubject('Our Code World Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('Helvetica', '', 10); 
        $pdf->AddPage();

        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
          
        $peds= $conn->query($sql);

    while($row = $peds->fetch()) {
         
    $cliente = ($row['cliente']);
    $fecha_1 = ($row['fecha']);
    $cuenta = ($row['cuenta']);
    $fecha_2 = ($row['devolucion']);
    $status = ($row['status_pedido']);
    $folio_1 = ($row['folio']);
    $subtotal_1 = ($row['subtotal']);
    $subtotal_2 = ($row['subtotal2']);
    $descuento =($row['descuento']);
    $impuesto = ($row['impuesto']);
    $total = ($row['total']);
    $comentario_1 = ($row['comentarios']);
    $direccion_entrega = ($row['direccion_entrega']);
    $pedido_id = $row['pedido'];
    $servicio = $row['servicioentrega'];
    }

    $content = ''; 
     
    $content .= ' 

        
        <div class="row"> 
        <div><label class="label8" style="" >Fecha de Impresión:   '.date("d/m/Y").'</label></div>
        <br/>
        <label class="label6" for="" bgcolor="#E4DBDA">Cliente: '.$cliente.'</label>&nbsp;&nbsp;<label class="label7" for="" bgcolor="#E4DBDA">Cuenta:       '.$cuenta.'</label><br/>
        <br/>
        <label class="label8" for="" bgcolor="#E4DBDA">Pedido:     '.$pedido_id.'</label>&nbsp;&nbsp;<label class="label9" for="" bgcolor="#E4DBDA">Fecha:     '.$fecha_1.'</label><br/>
           <br/>
        <label class="label10" for="" bgcolor="#E4DBDA">Devolucion:     '.$fecha_2.'</label>&nbsp;&nbsp;<label class="label11" for="" bgcolor="#E4DBDA">Folio:     '.$folio_1.'</label><br/>
           <br/>
           <label class="label12" for="" bgcolor="#E4DBDA">Status:     '.$status.'</label>&nbsp;&nbsp;<label class="label13" for="" bgcolor="#E4DBDA">Direccion de Entrega:     '.$direccion_entrega.'</label><br/>



            <div class="col-md-12"> 
                <h1 style="text-align:center;">REPORTE DE PEDIDOS</h1> 
            
                    <table border="1" cellpadding="5"> 
                      <thead> 
                        <tr align="center"> 
                         <th bgcolor="#E4DBDA">Cant</th>
                         <th bgcolor="#E4DBDA">Equipo</th>
                         <th bgcolor="#E4DBDA">Clave</th>
                         <th bgcolor="#E4DBDA">Días</th>
                         <th bgcolor="#E4DBDA">PU</th>
                         <th bgcolor="#E4DBDA">Importe</th>
                        </tr> 
                      </thead> 
                      '; 
                      $sql2="SELECT * from pedidos where pedido='".$pedido."'";
                      $manager = $this->getDoctrine()->getManager();
                      $conn = $manager->getConnection();
                      $peds= $conn->query($sql2);
                      while($row = $peds->fetch()){ 

                      $content .= ' 
                              <tr> 
                          <td>'.$row['cant'].'</td> 
                          <td>'.$row['equipo'].'</td> 
                          <td>'.$row['clave'].'</td> 
                          <td>'.$row['dias'].'</td> 
                          <td>'.$row['PU'].'</td> 
                          <td>'.$row['importe'].'</td> 
                      </tr> 
                      '; 
                      } 

                      $content .= '</table>'; 
     
    $content .= ' 
        <div class="row padding"> 
            <div class="col-md-12" style="text-align:center;"> 
                </div> 
               
                </div> 
                Comentarios:<input class="inp" id="inpt1" type="text" value="">'.$comentario_1.'</input>

                 <div class="col-md-12_total" style="text-align:right;"> 
                Subtotal:<input class="inp" id="inpt1" type="text" value="">'.$subtotal_1.'</input>
                </br>
                </div>
                 <div class="col-md-12_total2" style="text-align:right;"> 
                Descuento %:<input class="inp" id="inpt2" type="text" value="">'.$descuento.'</input>
                </br></div>
                 <div class="col-md-12_total3" style="text-align:right;"> 
                Subtotal:<input class="inp" id="inpt3" type="text" value="">'.$subtotal_2.'</input>
                </br></div>
                <div class="col-md-12_total3" style="text-align:right;"> 
                Servicio Entrega:<input class="inp" id="inpt10" type="text" value="">'.$servicio.'</input>
                </br></div>
                 <div class="col-md-12_total4" style="text-align:right;"> 
                Impuestos:<input class="inp" id="inpt4" type="text" value="">'.$impuesto.'</input>
                </br></div>
                 <div class="col-md-12_total5" style="text-align:right;"> 
                Total:<input class="inp" id="inpt5" type="text" value="">'.$total.'</input></br></div>
         
    '; 
    $pdf->writeHTML($content, true, 0, true, 0); 
    $pdf->lastPage(); 
    $pdf->output('Reporte_Pedido.pdf', 'I'); 
         
     }


public function reporteticketAction($pedido,Request $request)
     {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }

      $sql="SELECT * from pedidos where pedido='".$pedido."'";


        $pdf = $this->get("white_october.tcpdf")->create(PDF_PAGE_ORIENTATION, PDF_UNIT, array(79.50, 575), true, 'UTF-8', false);
        $pdf->SetAuthor('JJC');
        $pdf->SetTitle(('Reporte_Pedidos'));
        $pdf->SetSubject('Our Code World Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('Helvetica', '', 10); 
        $pdf->AddPage();

        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();          
        $peds= $conn->query($sql);

    while($row = $peds->fetch()) {
      $cliente1 = ($row['cliente']);
      $fecha_1 = ($row['fecha']);
      $cuenta = ($row['cuenta']);
      $fecha_2 = ($row['devolucion']);
      $status = ($row['status_pedido']);
      $folio_1 = ($row['folio']);
      $subtotal_1 = ($row['subtotal']);
      $subtotal_2 = ($row['subtotal2']);
      $descuento =($row['descuento']);
      $impuesto = ($row['impuesto']);
      $total = ($row['total']);
      $comentario_1 = ($row['comentarios']);
      $direccion_entrega = ($row['direccion_entrega']);
      $pedido_id = $row['pedido'];
      $servicio = $row['servicioentrega'];
      $dias = $row['dias'];
    }

      $sql2="SELECT cliente,telefono,comentarios from clientes where razon_social='".$cliente1."'";
      $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
          
        $peds2= $conn->query($sql2);
        while($row2 = $peds2->fetch()) {
          $telefono = ($row2['telefono']);
          $cliente = ($row2['cliente']);
          $comentarios = ($row2['comentarios']);
        }


        $sql3="SELECT domicilio from cuentas_cliente where cliente='".$cliente."' and cuenta_id='".$cuenta."'";
        $peds3= $conn->query($sql3);
        while($row3 = $peds3->fetch()) {
          $domicilio = ($row3['domicilio']);
        }

        $content = ''; 
        $content .= ' 
      
        
        <label class="lblticket"  style="font-size:12; text-align:center;">Andamios de Cholula</label><br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="lblticket1"  style="font-size:12; text-align:center;">Control de Rentas</label>
        <div class="row"> 
         <div><label class="label8" style="" >Renta:     '.$pedido_id.'</label></div>
        <div><label class="label6">Cliente: '.$cliente1.'</label></div>
        <div><label class="label8" style="" >Fecha de Impresión:   '.date("d/m/Y").'</label></div>
        <div><label class="label8" style="" >Obra:   '.$cuenta.'</label></div>
        <br>
        <div><label class="label13">Direccion de Entrega:     '.$domicilio.'</label><br/></div>
        <div><label class="label13">Indicaciones de la Obra:     '.$comentarios.'</label><br/></div>
         <div><label class="label15">Teléfono:  '.$telefono.'</label><br/></div>
         <div><label class="label16">Días de Renta:  '.$dias.'</label><br/></div>
         <hr>
        <br>
          <div class="col-md-12"> 
            
                    <table border="1" cellpadding="5"> 
                      <thead> 
                        <tr align="center"> 
                         <th>Cant</th>
                         <th>Equipo</th>
                        </tr> 
                      </thead> 
                      '; 
                      $sql2="SELECT * from pedidos where pedido='".$pedido."'";
                      $manager = $this->getDoctrine()->getManager();
                      $conn = $manager->getConnection();
                      $peds= $conn->query($sql2);
                      while($row = $peds->fetch()){ 

                      $content .= ' 
                              <tr> 
                          <td>'.$row['cant'].'</td> 
                          <td>'.$row['equipo'].'</td> 
                      </tr> 
                      '; 
                      } 

                      $content .= '</table>'; 

     
    $content .= ' 
    <br>
    </div>  
    Total:<label style=>'.$total.'</div>
    <input type="checkbox" name="vehicle" value="Bike"> Por Pagar<br>
    <br>
                Comentarios de la Renta:<label>'.$comentario_1.'</label>
                  <br>
                  <br>
                  <hr>                
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="lblticket"  style="font-size:12; text-align:center;">Firma de Salida</label>

    '; 
    $pdf->writeHTML($content, true, 0, true, 0); 
    $pdf->lastPage(); 
    $pdf->output('Reporte_Pedido.pdf', 'I'); 
         
     }






     public function reportepedidoarchivoAction($pedido,Request $request)
     {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }

      $sql="SELECT * from pedidos_archivados where pedido='".$pedido."'";

        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('JJC');
        $pdf->SetTitle(('Reporte_Pedidos'));
        $pdf->SetSubject('Our Code World Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('Helvetica', '', 10); 
        $pdf->AddPage();

        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
          
        $peds= $conn->query($sql);

    while($row = $peds->fetch()) {
         
    $cliente = ($row['cliente']);
    $fecha_1 = ($row['fecha']);
    $cuenta = ($row['cuenta']);
    $fecha_2 = ($row['devolucion']);
    $status = ($row['status_pedido']);
    $folio_1 = ($row['folio']);
    $subtotal_1 = ($row['subtotal']);
    $subtotal_2 = ($row['subtotal2']);
    $descuento =($row['descuento']);
    $impuesto = ($row['impuesto']);
    $total = ($row['total']);
    $comentario_1 = ($row['comentarios']);
    $direccion_entrega = ($row['direccion_entrega']);
    $pedido_id = $row['pedido'];
    $servicio = $row['servicioentrega'];
    }

    $content = ''; 
     
    $content .= ' 

        
        <div class="row"> 
        <label class="label6" for="" bgcolor="#E4DBDA">Cliente: '.$cliente.'</label>&nbsp;&nbsp;<label class="label7" for="" bgcolor="#E4DBDA">Cuenta:       '.$cuenta.'</label><br/>
        <br/>
        <label class="label8" for="" bgcolor="#E4DBDA">Pedido:     '.$pedido_id.'</label>&nbsp;&nbsp;<label class="label9" for="" bgcolor="#E4DBDA">Fecha:     '.$fecha_1.'</label><br/>
           <br/>
        <label class="label10" for="" bgcolor="#E4DBDA">Devolucion:     '.$fecha_2.'</label>&nbsp;&nbsp;<label class="label11" for="" bgcolor="#E4DBDA">Folio:     '.$folio_1.'</label><br/>
           <br/>
           <label class="label12" for="" bgcolor="#E4DBDA">Status:     '.$status.'</label>&nbsp;&nbsp;<label class="label13" for="" bgcolor="#E4DBDA">Direccion de Entrega:     '.$direccion_entrega.'</label><br/>



            <div class="col-md-12"> 
                <h1 style="text-align:center;">REPORTE DE PEDIDOS</h1> 
            
                    <table border="1" cellpadding="5"> 
                      <thead> 
                        <tr align="center"> 
                         <th bgcolor="#E4DBDA">Cant</th>
                         <th bgcolor="#E4DBDA">Equipo</th>
                         <th bgcolor="#E4DBDA">Clave</th>
                         <th bgcolor="#E4DBDA">Días</th>
                         <th bgcolor="#E4DBDA">PU</th>
                         <th bgcolor="#E4DBDA">Importe</th>
                        </tr> 
                      </thead> 
                      '; 
                      $sql2="SELECT * from pedidos_archivados where pedido='".$pedido."'";
                      $manager = $this->getDoctrine()->getManager();
                      $conn = $manager->getConnection();
                      $peds= $conn->query($sql2);
                      while($row = $peds->fetch()){ 

                      $content .= ' 
                              <tr> 
                          <td>'.$row['cant'].'</td> 
                          <td>'.$row['equipo'].'</td> 
                          <td>'.$row['clave'].'</td> 
                          <td>'.$row['dias'].'</td> 
                          <td>'.$row['PU'].'</td> 
                          <td>'.$row['importe'].'</td> 
                      </tr> 
                      '; 
                      } 

                      $content .= '</table>'; 
     
    $content .= ' 
        <div class="row padding"> 
            <div class="col-md-12" style="text-align:center;"> 
                </div> 
               
                </div> 
                Comentarios:<input class="inp" id="inpt1" type="text" value="">'.$comentario_1.'</input>

                 <div class="col-md-12_total" style="text-align:right;"> 
                Subtotal:<input class="inp" id="inpt1" type="text" value="">'.$subtotal_1.'</input>
                </br>
                </div>
                 <div class="col-md-12_total2" style="text-align:right;"> 
                Descuento %:<input class="inp" id="inpt2" type="text" value="">'.$descuento.'</input>
                </br></div>
                 <div class="col-md-12_total3" style="text-align:right;"> 
                Subtotal:<input class="inp" id="inpt3" type="text" value="">'.$subtotal_2.'</input>
                </br></div>
                <div class="col-md-12_total3" style="text-align:right;"> 
                Servicio Entrega:<input class="inp" id="inpt10" type="text" value="">'.$servicio.'</input>
                </br></div>
                 <div class="col-md-12_total4" style="text-align:right;"> 
                Impuestos:<input class="inp" id="inpt4" type="text" value="">'.$impuesto.'</input>
                </br></div>
                 <div class="col-md-12_total5" style="text-align:right;"> 
                Total:<input class="inp" id="inpt5" type="text" value="">'.$total.'</input></br></div>
         
    '; 
    $pdf->writeHTML($content, true, 0, true, 0); 
    $pdf->lastPage(); 
    $pdf->output('Reporte_Pedido.pdf', 'I'); 
         
     }


    public function ReportecotizacionAction($cotizacion,Request $request)
     {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }

      $sql="SELECT * from cotizaciones where cotizacion='".$cotizacion."'";

        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('JJC');
        $pdf->SetTitle(('Reporte_Cotización'));
        $pdf->SetSubject('Our Code World Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('Helvetica', '', 10); 
        $pdf->AddPage();

        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
          
        $peds= $conn->query($sql);

    while($row = $peds->fetch()) {
         
    $cliente = ($row['cliente']);
    $fecha_1 = ($row['fecha']);
    $cuenta = ($row['obra']);
    $fecha_2 = ($row['devolucion']);
    $cotizacion = ($row['cotizacion']);
    $status = ($row['status']);
    $subtotal_1 = ($row['subtotal']);
    $subtotal_2 = ($row['subtotal2']);
    $descuento =($row['descuento']);
    $impuesto = ($row['impuesto']);
    $total = ($row['total']);
    $comentario_1 = ($row['comentarios']);
    $direccion_entrega = ($row['direccion_entrega']);
    $servicio = $row['servicioentrega'];
    }

    $content = ''; 
     
    $content .= ' 
        
        <div class="row"> 
          <label class="label6"  style="color:#00BFFF; font-size:12;">Cliente: '.$cliente.'</label>
        </div>
        <div class="row"> 
          <label class="label7" for="" style="color:#00BFFF; font-size:12;">Cuenta: '.$cuenta.'</label>
        </div>
        <div class="row"> 
        <  label class="label8" for="" style="color:#00BFFF; font-size:12;">Cotización: '.$cotizacion.'</label>
        </div>
        <div class="row"> 
          <  label class="label9" for="" style="color:#00BFFF; font-size:12;">Fecha: '.$fecha_1.'</label>
        </div>
        <div class="row"> 
          <  label class="label10" for="" style="color:#00BFFF; font-size:12;">Devolucion: '.$fecha_2.'</label>
        </div>
        <div class="row"> 
           <label class="label12" for="" style="color:#00BFFF; font-size:12;">Direccion de Entrega: '.$direccion_entrega.'</label>
        </div>
<<<<<<< HEAD
=======

>>>>>>> 0aa17be247dff26b6ff897bba0f7e06438537884


            <div class="col-md-12"> 
                <h1 style="text-align:center;">REPORTE DE COTIZACIÓN</h1> 
            
                    <table border="1" cellpadding="5"> 
                      <thead> 
                        <tr align="center"> 
                         <th bgcolor="#00BFFF" style="font-size:100%;text-align:center;">Cantidad</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;text-align:center;">Equipo</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;text-align:center;">Clave</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;text-align:center;">Días</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;text-align:center;">PU</th>
                         <th bgcolor="#00BFFF" style="font-size:100%;text-align:center;">Importe</th>
                        </tr> 
                      </thead> 
                      '; 
                      $sql2="SELECT * from cotizaciones where cotizacion='".$cotizacion."'";
                      $manager = $this->getDoctrine()->getManager();
                      $conn = $manager->getConnection();
                      $peds= $conn->query($sql2);
                      while($row = $peds->fetch()){ 

                      $content .= ' 
                              <tr> 
                          <td style= "text-align:center;">'.$row['cantidad'].'</td> 
                          <td style= "text-align:center;">'.$row['equipo'].'</td> 
                          <td style= "text-align:center;">'.$row['clave'].'</td> 
                          <td style= "text-align:center;">'.$row['dias'].'</td> 
                          <td style= "text-align:center;">'.$row['PU'].'</td> 
                          <td style= "text-align:center;">'.$row['importe'].'</td> 
                      </tr> 
                      '; 
                      } 

                      $content .= '</table>'; 
     
    $content .= ' 
        <div class="row padding"> 
            <div class="col-md-12" style="text-align:center;"> 
                </div> 
                Comentarios:<input class="inp" id="inpt1" type="text" value="">'.$comentario_1.'</input>
                </div> 

              <div class="col-md-12_total" style="text-align:right;"> 
                <label class="label13"  style="font-size:12;">Subtotal: '.$subtotal_1.'</label>
              </div>
              
              <div class="col-md-12_total2" style="text-align:right;"> 
                <label class="label14"  style="font-size:12;">Descuento: '.$descuento.'</label>
              </div>

              <div class="col-md-12_total3" style="text-align:right;"> 
                <label class="label15"  style="font-size:12;">Subtotal: '.$subtotal_2.'</label>
              </div>

              <div class="col-md-12_total3" style="text-align:right;"> 
                <label class="label16"  style="font-size:12;">Servicio de Entrega: '.$servicio.'</label>
              </div>

              <div class="col-md-12_total4" style="text-align:right;"> 
                <label class="label17"  style="font-size:12;">Impuestos: '.$impuesto.'</label>
              </div>
              
              <div class="col-md-12_total5" style="text-align:right;"> 
                <label class="label18"  style="font-size:16;color:red">Total a Pagar: '.$total.'$</label>
              </div>
         
    '; 
    $pdf->writeHTML($content, true, 0, true, 0); 
    $pdf->lastPage(); 
    $pdf->output('Reporte_Cotización.pdf', 'I'); 
         
     }

     public function actstatusdevAction(Request $request)
     {
         $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        $pedido=$_POST['pedido'];
        $estado=$_POST['estado'];
        $idp=0;
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $conn->query("UPDATE devoluciones SET status_pedido='$estado' where pedido=$pedido");
        $em = $this->getDoctrine()->getManager();
                    $queryc = $em->createQuery(
                    'SELECT p
                    FROM GestionBundle:Pedidos p
                    WHERE p.id = :consultaBusqueda'
                    )->setParameter('consultaBusqueda', $pedido);
                    $query2 = $queryc->getResult();
                    foreach($query2 as $entity){
                            $idp= $entity->getPedido();
                        }
        $conn->query("UPDATE pedidos SET status_pedido='$estado' where pedido=$idp");
         

     }

public function modifpedcanceladoAction(Request $request)
{
 $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
        $pedido = $_POST['pedido']; 
        $comentarios = $_POST['razoncanc']; 
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();


        $eliminar= $conn->query("DELETE FROM pedidos_entregados where pedido=".$pedido);   
        $eliminar2= $conn->query("DELETE FROM pedidos where pedido=".$pedido);   
        $eliminar3= $conn->query("DELETE FROM detalles_pago where pedido=".$pedido);
        $eliminar4= $conn->query("DELETE FROM detalles_devoluciones where pedidosistema=".$pedido);   
        $eliminar5= $conn->query("DELETE FROM montospedidos where pedido=".$pedido);   
        return new JsonResponse('Cancelado'); 
  }
  public function archivarAction(Request $request)
  {
   $session = $request->getSession(); 
          if(!$session->get("usuarionombre")){
              $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
              return $this->redirect($this->generateUrl('usuario_login'));
          }
            $pedido = $_POST['pedido']; 
            $generardatos = array();
            $manager = $this->getDoctrine()->getManager();
            $conn = $manager->getConnection();
                    $em = $this->getDoctrine()->getManager();
                    $queryc = $em->createQuery(
                    'SELECT p
                    FROM GestionBundle:Pedidos p
                    WHERE p.pedido = :consultaBusqueda'
                    )->setParameter('consultaBusqueda', $pedido);
                    $query2 = $queryc->getResult();
                    if(count($query2)>0){
                    foreach($query2 as $entity){
                            $folio= $entity->getFolio();
                            $cliente= $entity->getCliente();
                            $cuenta= $entity->getCuenta();
                            $fecha= $entity->getFecha();
                            $devolucion= $entity->getDevolucion();
                            $stpedido=5;
                            $cant= $entity->getCant();
                            $clave= $entity->getClave();
                            $equipo= $entity->getEquipo();
                            $dias= $entity->getDias();
                            $pu= $entity->getPU();
                            $importe= $entity->getImporte();
                            $direccionent= $entity->getDireccionEntrega();
                            $comentarios= $entity->getComentarios();
                            $descuento= $entity->getDescuento();
                            $impuesto= $entity->getImpuesto();
                            $total= $entity->getTotal();
                            $subtotal= $entity->getSubtotal();
                            $subtotal2= $entity->getSubtotal2();
                            $servicioen= $entity->getServicioEntrega();
                            $porpagar= $entity->getPorPagar();
                            $mont= $entity->getMontototalpedido();
                            $montpago= $entity->getMontototalpago();

                  $insertar = $conn->query("INSERT INTO pedidos_archivados (pedido,montototalpago,por_pagar,montototalpedido,folio,cliente,cuenta,fecha,devolucion,cant,clave,equipo,dias,PU,importe,direccion_entrega,comentarios,descuento,impuesto,total,subtotal,subtotal2,servicioentrega,status_pedido) VALUES ($pedido,$montpago,$porpagar,$mont,'$folio','$cliente','$cuenta','$fecha','$devolucion',$cant,'$clave','$equipo',$dias,$pu,$importe,'$direccionent','$comentarios',$descuento,$impuesto,$total,$subtotal,$subtotal,$servicioen,$stpedido)");
                  $eliminar= $conn->query("DELETE FROM pedidos where pedido=".$pedido); 
                     }
                }
                return new JsonResponse('Pedido Archivado'); 
}

  public function eliminarrecoleccionAction(Request $request)
    {
          $session = $request->getSession(); 
            if(!$session->get("usuarionombre")){
              $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
              return $this->redirect($this->generateUrl('usuario_login'));
          }
        $pedido=$_POST['pedido'];
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
           $conn->query("UPDATE pedidos SET fecha_recoleccion='' WHERE pedido=".$pedido.""); 
        //$eliminar= $conn->query("DELETE FROM recolecciones where pedidos=".$pedido."");    
    }

    public function consultapdarchivadoAction(Request $request)
    {
      
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
          return $this->render('pedidos/pedidosarchivados.html.twig');
    }
    public function consultapdarchAction(Request $request)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }

      $sql=" SELECT DISTINCT p.cliente,p.cuenta,p.folio,p.pedido,p.fecha,p.devolucion FROM pedidos_archivados p";
      $con=0;
      $cliente=$_POST['cliente'];
      $folio=$_POST['folio'];
      $pedido=$_POST['pedido'];
      $desde=$_POST['desde'];
      $hasta=$_POST['hasta'];
      $desdedev=$_POST['desdedev'];
      $hastadev=$_POST['hastadev'];

      $añod1 = substr($desde,6,4);
      $diad1 = substr($desde,0,2);
      $mesd1 = substr($desde,3,2);
      $desde2=$añod1."-".$mesd1."-".$diad1;

      $año2 = substr($hasta,6,4);
      $dia2 = substr($hasta,0,2);
      $mes2 = substr($hasta,3,2);
      $hasta2=$año2."-".$mes2."-".$dia2;

      $añodev = substr($desdedev,6,4);
      $diadev = substr($desdedev,0,2);
      $mesdev = substr($desdedev,3,2);
      $desdedev1=$añodev."-".$mesdev."-".$diadev;

      $añodev2 = substr($hastadev,6,4);
      $diadev2 = substr($hastadev,0,2);
      $mesdev2 = substr($hastadev,3,2);
      $hastadev1=$añodev2."-".$mesdev2."-".$diadev2;

      $con=0;
      if ($cliente<>'')
        {
          $sql= $sql." and p.cliente like '".$cliente."%'";
          $con=1;
        }
        if ($folio<>'')
        {
          if($con==1){
            $sql= $sql." and p.folio like '".$folio."%'";
          }else{
            $sql= $sql." where p.folio like '".$folio."%'";
          }
          $con=2;
        }

        if ($pedido<>''){
          if($con==1 or $con==2){
            $sql= $sql." and p.pedido like '".$pedido."%'";
          }else{
              $sql= $sql." where p.pedido like '".$pedido."%'";
          }
          $con=3;
        }


         if ($desde <> '' and $hasta <> ''){
            if($con==1 or $con==2 or $con==3){
              $sql= $sql." and  str_to_date(p.fecha, '%d/%m/%Y') between cast('".$desde2."' as date) and cast('".$hasta2."' as date)";
            }else{
              $sql= $sql." where  str_to_date(p.fecha, '%d/%m/%Y') between cast('".$desde2."' as date) and cast('".$hasta2."' as date)";
            }
            $con=4;
          }

        if ($desdedev <> '' and $hastadev <> ''){
          if($con==1 or $con==2 or $con==3 or $con==4){
              $sql= $sql." and  str_to_date(p.devolucion, '%d/%m/%Y') between cast('".$desdedev1."' as date) and cast('".$hastadev1."' as date)";
            }else{
              $sql= $sql." where  str_to_date(p.devolucion, '%d/%m/%Y') between cast('".$desdedev1."' as date) and cast('".$hastadev1."' as date)";
            }
        }
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $dts= $conn->query($sql)->fetchAll();
        return new JsonResponse($dts); 
      }

      public function agregarbitacoraAction(Request $request)
      {
         $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $comentarios=$_POST['comentario'];
        $pedido=$_POST['pedido'];
        $usuario=$session->get("ID");
        $fecha=date("Y-m-d");
        $hora=date('h:i:s A');
        $insertar = $conn->query("INSERT INTO bitacora_pedido (pedido,fecha,usuario,comentarios,hora) VALUES ($pedido,'$fecha',$usuario,'$comentarios','$hora')"); 
        return new JsonResponse($insertar);   
      }

      public function detallesbitacoraAction(Request $request)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
        $id=$_POST['id'];
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $detalles= $conn->query("SELECT comentarios from bitacora_pedido WHERE id=$id")->fetchAll();
        return new JsonResponse($detalles);
      }
}
