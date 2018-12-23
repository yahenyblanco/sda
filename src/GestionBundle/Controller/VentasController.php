<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\Ventas;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ventas controller.
 *
 */
class VentasController extends Controller
{
	 public function HomeventasAction(Request $request,$id)
	 {
	 	 $session = $request->getSession(); 
	      if(!$session->get("usuarionombre")){
	          $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
	          return $this->redirect($this->generateUrl('usuario_login'));
	     }
	     $manager = $this->getDoctrine()->getManager();
       $conn = $manager->getConnection();
       $paso='No Activo';
       $cots = $conn->query("SELECT equipo,clave,precioventa FROM catalogo")->fetchAll(); 
       $clientes = $conn->query("SELECT c.razon_social,ct.domicilio,ct.cuenta_id FROM cuentas_cliente ct,clientes c where ct.cliente=c.cliente and c.desactivar=0")->fetchAll(); 
       $datosventas = $conn->query("SELECT DISTINCT v.nroVenta,c.razon_social as cliente,ct.cuenta_id, DATE_FORMAT(v.fecha,'%d/%m/%Y') as Fecha,g.equipo,v.cantEquipo,g.clave,g.precioventa,(v.cantEquipo * g.precioventa) as Importe,v.descuento,v.costoEntrega,v.iva FROM ventas v,clientes c, cuentas_cliente ct,catalogo g WHERE v.idcliente=c.cliente  and g.id=v.idEquipo  and v.idCuenta=ct.id and v.nroVenta=".$id)->fetchAll(); 

       if (count($datosventas)== 0){
        $datosventas = $conn->query("SELECT DISTINCT v.idCuenta, v.nroVenta,c.razon_social as cliente,ct.cuenta_id, DATE_FORMAT(v.fecha,'%d/%m/%Y') as Fecha,g.equipo,v.cantEquipo,g.clave,g.precioventa,(v.cantEquipo * g.precioventa) as Importe,v.descuento,v.costoEntrega,v.iva FROM archivos_ventas v,clientes c, cuentas_cliente ct,catalogo g WHERE v.idcliente=c.cliente and g.id=v.idEquipo and ct.id=v.idCuenta and v.nroVenta=".$id)->fetchAll(); 
          $paso='1';

       }

        return $this->render('ventas/nuevaventa.html.twig',array('datosequipos' => $cots,'clientes' => $clientes,'id' => $id,'datosventas' => $datosventas,'archivado' => $paso));

	 }

	 


	 public function AddequiposAction(Request $request)
	 {
	 	 $session = $request->getSession(); 
	      if(!$session->get("usuarionombre")){
	          $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
	          return $this->redirect($this->generateUrl('usuario_login'));
	     }
	 }
	 public function UltVentaAction(Request $request){
	 	$session = $request->getSession(); 
	      if(!$session->get("usuarionombre")){
	          $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
	          return $this->redirect($this->generateUrl('usuario_login'));
	     }
	      $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();

	    $id= $conn->query("SELECT id FROM ventas  order by id desc limit 1")->fetchAll();
        
         if (count($id)== 0){
            $ultid=1;    
         }else{
         	$ultid=$id[0]['id'];
         }
         return new JsonResponse($ultid);
	}
	 public function AddventaAction(Request $request)
	 {
	 	 $session = $request->getSession(); 
	      if(!$session->get("usuarionombre")){
	          $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
	          return $this->redirect($this->generateUrl('usuario_login'));
	     }


	      $cliente = ($_POST['cliente']);
          $obra = ($_POST['obra']);
          $fecha = ($_POST['fecha']);
          $descuento = ($_POST['descuento']);
          $entrega = ($_POST['entrega']);
          $iva = ($_POST['iva']);
          $claveequipo = ($_POST['claveequipo']);
          $cantidad = ($_POST['cantidad']);
          $nroventa = ($_POST['nroventa']);

          //  $cliente = '480 edificaciones';
          // $obra = 'CU';
          // $fecha = '11/09/2018';
          // $descuento = 0;
          // $entrega = 0;
          // $iva = false;
          // $claveequipo = 'arn';
          // $cantidad = 1;
          // $nroventa = 12;

        $año2 = substr($fecha, 6,4);
        $dia2 = substr($fecha,0,2);
        $mes2 = substr($fecha,3,2);
        $fecha= $año2."-".$mes2."-".$dia2;
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();

       
         $idequipo= $conn->query("SELECT id FROM catalogo where clave='$claveequipo'")->fetchAll();
         $equipo=$idequipo[0]['id'];
         $idcliente= $conn->query("SELECT cliente FROM clientes where razon_social='$cliente'")->fetchAll();
         $cliente=$idcliente[0]['cliente'];
         $idobra= $conn->query("SELECT id FROM cuentas_cliente where cuenta_id='".$obra."' and cliente='".$cliente."'")->fetchAll();
         $obra=$idobra[0]['id'];

         $nrvt= $conn->query("SELECT id FROM archivos_ventas where nroVenta=".$nroventa)->fetchAll();
         if (count($nrvt)!= 0){
          $nroventa=$nroventa+1;

         }


         $insertar = $conn->query("INSERT INTO ventas (nroVenta,idCliente,idCuenta,idEquipo,cantEquipo,descuento,costoEntrega,status,iva,fecha) VALUES ($nroventa,$cliente,$obra,$equipo,$cantidad,$descuento,$entrega,'Generado',$iva,'$fecha')"); 
         return new JsonResponse($nroventa);   

	 }
     public function ArchivarVentaAction(Request $request){
     	  $session = $request->getSession(); 
	        if(!$session->get("usuarionombre")){
	          $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
	          return $this->redirect($this->generateUrl('usuario_login'));
	      }

	      $cliente = ($_POST['cliente']);
          $obra = ($_POST['obra']);
          $fecha = ($_POST['fecha']);
          $descuento = ($_POST['descuento']);
          $entrega = ($_POST['entrega']);
          $iva = ($_POST['iva']);
          $claveequipo = ($_POST['claveequipo']);
          $cantidad = ($_POST['cantidad']);
          $nroventa = ($_POST['nroventa']);

	      $año2 = substr($fecha, 6,4);
	      $dia2 = substr($fecha,0,2);
	      $mes2 = substr($fecha,3,2);
	      
	      $fecha= $año2."-".$mes2."-".$dia2;
	      $manager = $this->getDoctrine()->getManager();
	      $conn = $manager->getConnection();

       
          $idequipo= $conn->query("SELECT id FROM catalogo where clave='$claveequipo'")->fetchAll();
          if (count($idequipo)<> 0){
          	$claveequipo=$idequipo[0]['id'];
          }

          $idcliente= $conn->query("SELECT cliente FROM clientes where razon_social='$cliente'")->fetchAll();
          if (count($idcliente)<> 0){
          	$cliente=$idcliente[0]['cliente'];
          }
          $idobra= $conn->query("SELECT id FROM cuentas_cliente where cuenta_id='$obra'")->fetchAll();
          if (count($idobra)<> 0){
          $obra=$idobra[0]['id'];
          }


          $eliminar= $conn->query("DELETE FROM ventas where nroVenta=$nroventa");    

          $insertar = $conn->query("INSERT INTO archivos_ventas (nroVenta,idCliente,idCuenta,idEquipo,cantEquipo,descuento,costoEntrega,iva,fecha) VALUES ($nroventa,$cliente,$obra,$claveequipo,$cantidad,$descuento,$entrega,'$iva','$fecha')");    
          return new JsonResponse('Venta Archivada');
	 }
	public function DeleteVentasAction(Request $request){

		$session = $request->getSession(); 
	      if(!$session->get("usuarionombre")){
	          $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
	          return $this->redirect($this->generateUrl('usuario_login'));
	     }
		$nroventa = ($_POST['nroventa']);
		$manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $eliminar= $conn->query("DELETE FROM ventas where nroVenta=$nroventa");    
        return new JsonResponse('Venta Eliminada');


	}	 

	public function FiltrosventaAction(Request $request){

	 	 $session = $request->getSession(); 
	      if(!$session->get("usuarionombre")){
	          $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
	          return $this->redirect($this->generateUrl('usuario_login'));
	     }
	     $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
	      $clientes = $conn->query("SELECT c.razon_social,ct.domicilio,ct.cuenta_id FROM cuentas_cliente ct,clientes c where ct.cliente=c.cliente")->fetchAll(); 
        return $this->render('ventas/filtroventas.html.twig',array('clientes' => $clientes));

	 }

    

	 public function GenerarfiltroAction(Request $request){

	 	$session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }

      
      $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
       $sql="SELECT DISTINCT v.nroVenta,c.razon_social as cliente,ct.cuenta_id, DATE_FORMAT(v.fecha,'%d/%m/%Y') as Fecha FROM ventas v,clientes c, cuentas_cliente ct WHERE v.idcliente=c.cliente and ct.cliente=c.cliente ";
          
      $cliente=$_POST['cliente'];
      $nroventa=$_POST['nroventa'];
      $fecha_desde=$_POST['fecha_desde'];
      $fecha_hasta=$_POST['fecha_hasta'];

      //  $cliente='480 edificaciones';
      // $nroventa='';
      // $fecha_desde='';
      // $fecha_hasta='';

      $añod = substr($fecha_desde,6,4);
      $diad = substr($fecha_desde,0,2);
      $mesd = substr($fecha_desde,3,2);
      $fecha_desde1=$añod."-".$mesd."-".$diad;

      $añod2 = substr($fecha_hasta,6,4);
      $diad2 = substr($fecha_hasta,0,2);
      $mesd2 = substr($fecha_hasta,3,2);
      $fecha_hasta2=$añod2."-".$mesd2."-".$diad2;


	  

       if ($cliente<>'')
        {
        $idcliente= $conn->query("SELECT cliente FROM clientes where razon_social='$cliente'")->fetchAll();
      	 $cliente=$idcliente[0]['cliente'];
          $sql= $sql." and v.idCliente= ".$cliente."";
        }
        if ($nroventa<>''){
            $sql= $sql." and v.nroVenta= ".$nroventa."";
        }

        
        if ($fecha_desde1 <> '--' and $fecha_hasta2 <> '--'){
              $sql= $sql." and v.fecha >= '".$fecha_desde1."' and v.fecha <= '".$fecha_hasta2."'";
        }
         if ($fecha_desde1 <> '--' and $fecha_hasta2 == '--') {
            $sql= $sql." and v.fecha = '".$fecha_desde."'";
        }
        $sql= $sql. " order by v.nroVenta";
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $dts= $conn->query($sql)->fetchAll();
        return new JsonResponse($dts); 
	 }


	 public function GenerarfiltroarchivoAction(Request $request){

	 	$session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }

      
      $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
       $sql="SELECT DISTINCT v.nroVenta,c.razon_social as cliente,ct.cuenta_id, DATE_FORMAT(v.fecha,'%d/%m/%Y') as Fecha FROM archivos_ventas v,clientes c, cuentas_cliente ct WHERE v.idcliente=c.cliente and ct.cliente=c.cliente ";
          
      $cliente=$_POST['cliente'];
      $nroventa=$_POST['nroventa'];
      $fecha_desde=$_POST['fecha_desde'];
      $fecha_hasta=$_POST['fecha_hasta'];

      //  $cliente='480 edificaciones';
      // $nroventa='';
      // $fecha_desde='';
      // $fecha_hasta='';

      $añod = substr($fecha_desde,6,4);
      $diad = substr($fecha_desde,0,2);
      $mesd = substr($fecha_desde,3,2);
      $fecha_desde1=$añod."-".$mesd."-".$diad;

      $añod2 = substr($fecha_hasta,6,4);
      $diad2 = substr($fecha_hasta,0,2);
      $mesd2 = substr($fecha_hasta,3,2);
      $fecha_hasta2=$añod2."-".$mesd2."-".$diad2;


	  

       if ($cliente<>'')
        {
        $idcliente= $conn->query("SELECT cliente FROM clientes where razon_social='$cliente'")->fetchAll();
      	 $cliente=$idcliente[0]['cliente'];
          $sql= $sql." and v.idCliente= ".$cliente."";
        }
        if ($nroventa<>''){
            $sql= $sql." and v.nroVenta = ".$nroventa."";
        }

        
        if ($fecha_desde1 <> '--' and $fecha_hasta2 <> '--'){
              $sql= $sql." and v.fecha >= '".$fecha_desde1."' and v.fecha <= '".$fecha_hasta2."'";
        }
         if ($fecha_desde1 <> '--' and $fecha_hasta2 == '--') {
            $sql= $sql." and v.fecha = '".$fecha_desde."'";
        }
        $sql= $sql. " order by v.nroVenta";
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $dts= $conn->query($sql)->fetchAll();
        return new JsonResponse($dts); 
	 }



	  public function datosventaAction(Request $request){
		    $session = $request->getSession(); 
	        if(!$session->get("usuarionombre")){
	            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
	            return $this->redirect($this->generateUrl('usuario_login'));
	        }
		  $manager = $this->getDoctrine()->getManager();
	      $conn = $manager->getConnection();
	      $nroventa=$_POST['nroventa'];
	      $datosventas= $conn->query("SELECT id,nroVenta,idCliente,idCuenta,idEquipo,cantEquipo,descuento,costoEntrega,iva,DATE_FORMAT(fecha,'%d/%m/%Y') as fecha from ventas WHERE nroVenta=".$nroventa."")->fetchAll();
	      return new JsonResponse($datosventas); 
	 }

	  public function datosventaarchivoAction(Request $request){
		    $session = $request->getSession(); 
	        if(!$session->get("usuarionombre")){
	            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
	            return $this->redirect($this->generateUrl('usuario_login'));
	        }
		  $manager = $this->getDoctrine()->getManager();
	      $conn = $manager->getConnection();
	      $nroventa=$_POST['nroventa'];
	      $datosventas= $conn->query("SELECT id,nroVenta,idCliente,idCuenta,idEquipo,cantEquipo,descuento,costoEntrega,iva,DATE_FORMAT(fecha,'%d/%m/%Y') as fecha from archivos_ventas WHERE nroVenta=".$nroventa."")->fetchAll();
	      return new JsonResponse($datosventas); 
	 }

	public function ArchivarventafiltroAction(Request $request){
	 	 $session = $request->getSession(); 
	      if(!$session->get("usuarionombre")){
	          $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
	          return $this->redirect($this->generateUrl('usuario_login'));
	     }
	     $manager = $this->getDoctrine()->getManager();
         $conn = $manager->getConnection();
	     $clientes = $conn->query("SELECT c.razon_social,ct.domicilio,ct.cuenta_id FROM cuentas_cliente ct,clientes c where ct.cliente=c.cliente")->fetchAll(); 
         return $this->render('ventas/filtroventasarchivo.html.twig',array('clientes' => $clientes));
	 }



	 public function reporteticketAction($nroventa,Request $request)
     {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }

      $sql="SELECT DISTINCT v.nroVenta,c.razon_social as cliente,v.idCuenta, DATE_FORMAT(v.fecha,'%d/%m/%Y') as Fecha FROM ventas v,clientes c, cuentas_cliente ct WHERE v.idcliente=c.cliente and ct.id=c.cliente and v.nroVenta=".$nroventa;


        $pdf = $this->get("white_october.tcpdf")->create(PDF_PAGE_ORIENTATION, PDF_UNIT, array(79.50, 575), true, 'UTF-8', false);
        $pdf->SetAuthor('JJC');
        $pdf->SetTitle(('Reporte_Ventas'));
        $pdf->SetSubject('Our Code World Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('Helvetica', '', 10); 
        $pdf->AddPage();

        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();          
        $peds= $conn->query($sql);

	    while($row = $peds->fetch()) {
	      $cliente1 = ($row['cliente']);
	      $fecha_1 = ($row['Fecha']);
	      $cuenta = ($row['idCuenta']);
	    }

       $sql2="SELECT cliente,telefono from clientes where razon_social='".$cliente1."'";
       $manager = $this->getDoctrine()->getManager();
       $conn = $manager->getConnection();
          
       $peds2= $conn->query($sql2);
        while($row2 = $peds2->fetch()) {
          $telefono = ($row2['telefono']);
          $cliente = ($row2['cliente']);
        }
        $sql3="SELECT domicilio,cuenta_id from cuentas_cliente where cliente='".$cliente."' and id='".$cuenta."'";
        $peds3= $conn->query($sql3);
        while($row3 = $peds3->fetch()) {
          $domicilio = ($row3['domicilio']);
          $cta = ($row3['cuenta_id']);
        }
        $content = ''; 
        $content .= ' 
      
        
        <label class="lblticket"  style="font-size:12; text-align:center;">Andamios de Cholula</label><br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="lblticket1"  style="font-size:12; text-align:center;">Venta: '.$nroventa.'</label>
        <div class="row"> 
         <div><label class="label8" style="" >Fecha de Impresión:   '.date("d/m/Y").'</label></div>
        <div><label class="label6">Cliente: '.$cliente1.'</label></div>
        
        <br>
        	<div><label class="label13">Direccion de Entrega:     '.$domicilio.'</label><br/></div>
         	<div><label class="label15">Teléfono:  '.$telefono.'</label><br/></div>
         	<hr>
        <br>
          <div class="col-md-12"> 
            
                    <table border="1" cellpadding="5"> 
                        <tr align="center"> 
                         <th bgcolor="#E4DBDA">Cant</th>
                         <th bgcolor="#E4DBDA">Equipo</th>
                         <th bgcolor="#E4DBDA">Importe</th>
                        </tr> 
                      '; 
                      $subtotal=0;
                      $entrega=0;
                      $descuento=0;
                      $iva=0;
                      $total=0;
                      $sql2="SELECT v.cantEquipo,c.equipo,v.descuento,v.costoEntrega,v.iva,c.precioventa,(v.cantEquipo * c.precioventa) as Importe from ventas v,catalogo c where c.id=v.idEquipo and nroVenta='".$nroventa."'";
                      $manager = $this->getDoctrine()->getManager();
                      $conn = $manager->getConnection();
                      $peds= $conn->query($sql2);
                      while($row = $peds->fetch()){ 
                      //$subtotal=$row['Importe'];
                      $content .= ' 
                       <tr> 
                          <td>'.$row['cantEquipo'].'</td> 
                          <td>'.$row['equipo'].'</td>
                          <td>'.$row['Importe'].'</td> 
                          
                      </tr> 
                      '; 
                      
                          $subtotal=$row['Importe']+$subtotal;
                        
                          $entrega=$row['costoEntrega'];
                          $descuento=$row['descuento'];
                          if ($row['iva']==1){
                            $iva=$subtotal * 0.16;
                          }
                      } 

    $content .= '</table>'; 
    $content .= ' 
    </div>
    Subtotal:<label style="color:red;" >$'.$subtotal.'</div>
    Descuento:<label style="color:red;">%'.$descuento.'</div>
    Costo Entrega:<label style="color:red;">$'.$entrega.'</div>
    Iva:<label style="color:red;">$'.$iva.'</div>
    ';
    
    $totaldescuento= $subtotal * $descuento /100;
    $total=($subtotal-$totaldescuento)+$entrega + $iva;
	  $content .= ' 

    Total:<label style="color:blue;">$'.$total.'</div>
    <br>
    <label>Comentarios:</label>
     
    ';
    $pdf->writeHTML($content, true, 0, true, 0); 
    $pdf->lastPage(); 
    $pdf->output('Reporte_Venta.pdf', 'I'); 
         
     }
}