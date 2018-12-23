<?php

namespace GestionBundle\Controller;
use GestionBundle\Entity\Clientes;
use GestionBundle\Entity\CuentasCliente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClientesController extends Controller
{
	 public function detallesAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
    	$manager = $this->getDoctrine()->getManager();
    	$conn = $manager->getConnection();
       $session = $request->getSession(); 
        $rol=$session->get("myrol");
    	$datosclientes= $conn->query("SELECT * FROM clientes where desactivar is null or desactivar = 0")->fetchAll();  
         return $this->render('clientes/catalogoclientes2.html.twig',array('datosclientes' => $datosclientes,'myrol' => $rol ));
    }

    public function detalles2Action(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
      $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
       $session = $request->getSession(); 
        $rol=$session->get("myrol");
      $datosclientes= $conn->query("SELECT * FROM clientes where desactivar != 1")->fetchAll();  
         return $this->render('clientes/catalogoclientes.html.twig',array('datosclientes' => $datosclientes,'myrol' => $rol ));
    }

     public function addAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
      $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
       $session = $request->getSession(); 
        $rol=$session->get("myrol");

      $datosclientes= $conn->query("SELECT * FROM clientes")->fetchAll();  
        if($request->getMethod()=="POST"){
            $razon = ($_POST['clientename']);
            $rfc = ($_POST['rfc']); 
            $domicilio = ($_POST['domicilio']);
            $ciudad = ($_POST['ciudad']);
            $comentarios = ($_POST['comentario']);
            $correo = ($_POST['correo']);            
         //   $razonrest = ($_POST['razonrest']);
            $telf1 = ($_POST['telefono1']);
            $telf2 = ($_POST['telefono2']);

                        $manager = $this->getDoctrine()->getManager();
                        $conn = $manager->getConnection();
                        $em = $this->getDoctrine()->getManager();
                        $cadena = new Clientes();

                        $cadena->setRazonSocial($razon);
                        $cadena->setRfc($rfc);
                        $cadena->setDomicilioFiscal($domicilio);
                        $cadena->setCiudad($ciudad);
                        $cadena->setComentarios($comentarios);
                        $cadena->setCorreo($correo);
                        $cadena->setTelefono($telf1);
                        $cadena->setTelefono2($telf2);
                        $cadena->setRestringir("Normal");
                        $cadena->setRazonRestriccion("Normal");
                        $cadena->setDesactivar(0);
                        $em=$this->getDoctrine()->getManager();
                        $em->persist($cadena);        
                        $em->flush();

                        $idcliente = $cadena->getCliente();
                        if(count($_POST['nombreobra'])>0){
                            for($i = 0; $i < count($_POST['nombreobra']); $i++){
                                    $obras = new CuentasCliente();
                                    $obras->setCuentaId($_POST['nombreobra'][$i]);
                                    $obras->setComentarios($_POST['obrascomentarios'][$i]);
                                    $obras->setStatus($_POST['obrasestado'][$i]);
                                    $obras->setDomicilio($_POST['obrasubicacion'][$i]);
                                    $obras->setCliente($idcliente);
                                    $em=$this->getDoctrine()->getManager();
                                    $em->persist($obras);        
                                    $em->flush();
                              }
                          }
                $this->get('session')->getFlashBag()->add('add','Cliente Almacenado');
                return $this->redirect($this->generateUrl('detalles_clientes'));

         }
         return $this->render('clientes/add.html.twig',array('datosclientes' => $datosclientes,'myrol' => $rol ));
    }

     public function editAction(Request $request,$id)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
      $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
       $session = $request->getSession(); 
        $rol=$session->get("myrol");
      $datosclientes =  $this->getDoctrine()->getRepository('GestionBundle:Clientes')->findOneBy(array("cliente"=>$id)); 
      $obrasclientes = $this->getDoctrine()->getRepository('GestionBundle:CuentasCliente')->findBy(array("cliente"=>$id)); 


         return $this->render('clientes/edit.html.twig',array('datosclientes' => $datosclientes,'myrol' => $rol, "obras"=>$obrasclientes));
    }


 public function editarobrasAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
 
    $obrasclientes = $this->getDoctrine()->getRepository('GestionBundle:CuentasCliente')->findOneBy(array("id"=>$_POST["idobrasprincipal"])); 


            $ubicacion = ($_POST['ubicacionobra']);
            $comentariosobra = ($_POST['comentariosobra']); 
            $cuentaid = $_POST["cuentaid"];

          //  $status = $_POST['obrasdesactivar'];
            if(isset($_POST['obrasdesactivar'])){
              $statusobra = "Normal";
            }else{
              $statusobra = "Inactiva";
            }

                        $obrasclientes->setDomicilio($ubicacion);
                        $obrasclientes->setComentarios($comentariosobra);                       
                        $obrasclientes->setStatus($statusobra);
                        $obrasclientes->setCuentaId($cuentaid);
                        $em=$this->getDoctrine()->getManager();                             
                        $em->flush();



        $respuesta = "Obra modificada";
        return new JsonResponse($respuesta); 


         
    }

     public function addobrasAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
 

            $ubicacion = ($_POST['ubicacionobra']);
            $comentariosobra = ($_POST['comentariosobra']); 
            $cuentaid = $_POST["cuentaid"];


          //  $status = $_POST['obrasdesactivar'];
            if(isset($_POST['obrasdesactivar'])){
              $statusobra = "Normal";
            }else{
              $statusobra = "Inactiva";
            }
            
                        $obrasclientes = new CuentasCliente();
                        $obrasclientes->setDomicilio($ubicacion);
                        $obrasclientes->setComentarios($comentariosobra);                       
                        $obrasclientes->setStatus($statusobra);
                        $obrasclientes->setCliente($_POST["idclientes"]);
                        $obrasclientes->setCuentaId($cuentaid);

                        $em=$this->getDoctrine()->getManager();    
                         $em->persist($obrasclientes);                       
                        $em->flush();

                        $localidad['id']               = $obrasclientes->getId();
                        $localidad['domicilio']        = $obrasclientes->getDomicilio();
                        $localidad['comentarios']      = $obrasclientes->getComentarios();
                        $localidad['status']           = $obrasclientes->getStatus();
                        $localidad['cuentaid']           = $obrasclientes->getCuentaId();
                        $localidad['mensaje']          = "Obra agregada";
                        $generardatos[]                = $localidad;
        
        return new JsonResponse($generardatos); 


         
    }

    public function editarclientesAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
    
      $datosclientes =  $this->getDoctrine()->getRepository('GestionBundle:Clientes')->findOneBy(array("cliente"=>$_POST["idclientes"])); 

            $razon = ($_POST['clientename']);
            $rfc = ($_POST['rfc']); 
            $domicilio = ($_POST['domicilio']);
            $ciudad = ($_POST['ciudad']);
            $comentarios = ($_POST['comentario']);
            $correo = ($_POST['correo']);            
         //   $razonrest = ($_POST['razonrest']);
            $telf1 = ($_POST['telefono1']);
            $telf2 = ($_POST['telefono2']);
           // $rs=($_POST['desactivar']);
                        $datosclientes->setRazonSocial($razon);
                        $datosclientes->setRfc($rfc);
                        $datosclientes->setDomicilioFiscal($domicilio);
                        $datosclientes->setCiudad($ciudad);
                        $datosclientes->setComentarios($comentarios);
                        $datosclientes->setCorreo($correo);
                        $datosclientes->setTelefono($telf1);
                        $datosclientes->setTelefono2($telf2);
                        if(!isset($_POST['restrigir'])){
                          $datosclientes->setRestringir("");
                           $datosclientes->setRazonRestriccion("");
                        }else{
                           $datosclientes->setRestringir("Restringido");
                           $datosclientes->setRazonRestriccion($_POST['comentariorazon']);
                          
                        }

                        $em=$this->getDoctrine()->getManager();                        
                        $em->flush();



        $respuesta = "Registro modificado";
        return new JsonResponse($respuesta); 


         
    }


     public function desactivarclienteAction(Request $request,$id)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
    
      $datosclientes =  $this->getDoctrine()->getRepository('GestionBundle:Clientes')->findOneBy(array("cliente"=>$id)); 

                        $datosclientes->setDesactivar(1);
                        $em=$this->getDoctrine()->getManager();                        
                        $em->flush();
   $this->get('session')->getFlashBag()->add('fail','El cliente '.$datosclientes->getCliente()." ".$datosclientes->getRazonSocial(). " fue eliminado del sistema.");
                return $this->redirect($this->generateUrl('detalles_clientes'));
         
    }

    public function eliminarobrasAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
    
      $obrasclientes = $this->getDoctrine()->getRepository('GestionBundle:CuentasCliente')->findOneBy(array("id"=>$_POST["idobrasprincipal"])); 
                        $em=$this->getDoctrine()->getManager();
                        $em->remove($obrasclientes);
                        $em->flush();



        $respuesta = "Registro eliminado";
        return new JsonResponse($respuesta); 
         
    }

       public function filtrarAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
      $con=0;
      $cliente=$_POST['cliente'];
      $razon=$_POST['razon'];
      $rfc=$_POST['rfc'];
      $restringir=$_POST['status'];
      $sql="SELECT * FROM clientes";
      
      if ($cliente <> ''){
		    $sql= $sql." where cliente like '".$cliente."%'";
		    $con=1;
	    }
	  if ($razon <> ''){
	   		 if ($con==1){
	      		$sql= $sql." and razon_social like '".$razon."%'";
	      		$con=2;
		  }else {
		    $sql=$sql. " where razon_social like '".$razon."%'"; 
		    $con=3;
	      }
		}

	  if ($rfc <> ''){
		  if ($con==1 OR $con==2 OR $con==3 ){
		    $sql= $sql." and rfc like '".$rfc."%'";
		      $con=4;
		  }else{
		    $sql=$sql. " where rfc like '".$rfc."%'"; 
		    $con=5;
		  }
		}
	  if ($restringir <> ''){
		  if ($con==1 OR $con==2 OR $con==3 OR $con==4 OR $con==5 ){
		    $sql= $sql." and restringir like '".$restringir."%'";
	  	  }else{
	    	$sql=$sql. " where restringir like '".$restringir."%'"; 
	  	   }
		}
		$manager = $this->getDoctrine()->getManager();
    $conn = $manager->getConnection();
		$filtros= $conn->query($sql)->fetchAll();
		return new JsonResponse($filtros); 
	}
	public function registrarAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
    $razon = ($_POST['razon']);
		$rfc = ($_POST['rfc']); 
		$domicilio = ($_POST['domicilio']);
		$ciudad = ($_POST['ciudad']);
		$comentarios = ($_POST['comentarios']);
		$correo = ($_POST['correo']);
		$rest = ($_POST['estado']);
		$razonrest = ($_POST['razonrest']);
		$telf1 = ($_POST['telf1']);
		$telf2 = ($_POST['telf2']);


		$manager = $this->getDoctrine()->getManager();
    $conn = $manager->getConnection();
    $em = $this->getDoctrine()->getManager();
    $dtscliente='';
                     $dtscliente = array();
                        $cadena = new Clientes();
                        $cadena->setRazonSocial($razon);
                        $cadena->setRfc($rfc);
                        $cadena->setDomicilioFiscal($domicilio);
                        $cadena->setCiudad($ciudad);
                        $cadena->setComentarios($comentarios);
                        $cadena->setCorreo($correo);
                        $cadena->setDesactivar('false');
                        $cadena->setRazonRestriccion($razonrest);
                        $cadena->setRestringir("Normal");
                        $cadena->setTelefono($telf1);
                        $cadena->setTelefono2($telf2);

                        $em=$this->getDoctrine()->getManager();
                        $em->persist($cadena);        
                        $em->flush();

            return new JsonResponse($dtscliente);
          }
				    
	

  public function idAction(Request $request)
  {
     $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
    $manager = $this->getDoctrine()->getManager();
    $conn = $manager->getConnection();
    $id= $conn->query("SELECT cliente FROM clientes  order by cliente desc limit 1")->fetchAll();  
        return new JsonResponse($id); 

  }
	public function registrarcuentaAction(Request $request)
		{
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
			$clienteid = ($_POST['clienteid']);
			$cuentaid = ($_POST['cuentaid']); 
			$comentarios = ($_POST['comentarios']);
			$domicilio = ($_POST['domicilio']);
			$status = ($_POST['status']);
      $mens='xx';
      $manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
      $id= $conn->query("SELECT id FROM cuentas_cliente  where cuenta_id='$cuentaid' and cliente=$clienteid ")->fetchAll();  
      if(empty($id)) {
       $cadena = new CuentasCliente();
                $cadena->setCliente($clienteid);
                $cadena->setCuentaId($cuentaid);
                $cadena->setComentarios($comentarios);
                $cadena->setDomicilio($domicilio);
                $cadena->setStatus($status);
                $em=$this->getDoctrine()->getManager();
                $em->persist($cadena);        
                $em->flush(); 
              }else{
                $mens='Registrado';
              }
              return new JsonResponse($mens); 
			 
                
		}
		

		public function consultarcuentaAction(Request $request)
		{
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
			$clienteid = ($_POST['clienteid']);

			$manager = $this->getDoctrine()->getManager();
      $conn = $manager->getConnection();
      $em = $this->getDoctrine()->getManager();

                    $queryc = $em->createQuery(
                    'SELECT p
                    FROM GestionBundle:Clientes p   
                    WHERE p.cliente = :price '
                    )->setParameter('price', $clienteid);
                     $query2 = $queryc->getResult();

                     foreach($query2 as $entity){
                       $razons= $entity->getRazonSocial();
                       $rfc= $entity->getRfc();
                       $dmfiscal= $entity->getDomicilioFiscal();
                       $comentarios= $entity->getComentarios();
                       $restr= $entity->getRestringir();
                       $razonrest= $entity->getRazonRestriccion();
                       $ciudad= $entity->getCiudad();
                       $correo= $entity->getCorreo();
                       $tlf1= $entity->getTelefono();
                       $tlf2= $entity->getTelefono2();
                       $localidad['razonsocial']  = $razons;
                        $localidad['rfc']  = $rfc;
                        $localidad['domicilio']  = $dmfiscal;
                        $localidad['comentarios']  = $comentarios;
                        $localidad['restringir']  = $restr;
                        $localidad['razonrestr']  = $razonrest;
                        $localidad['ciudad']  = $ciudad;
                        $localidad['correo']  = $correo;
                        $localidad['tlf1']  = $tlf1;
                        $localidad['tlf2']  = $tlf2;
                        $generardatos[] = $localidad;
                     }
                     return new JsonResponse($generardatos); 


		}

		public function detallescuentaAction(Request $request)
		{
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));

        }
			$clienteid = $_POST['clienteid'];
			$manager = $this->getDoctrine()->getManager();
        	$conn = $manager->getConnection();
        	$em = $this->getDoctrine()->getManager();
                    $queryc = $em->createQuery(
                    'SELECT p
                    FROM GestionBundle:CuentasCliente p   
                    WHERE p.cliente = :price '
                    )->setParameter('price', $clienteid);
                     $query2 = $queryc->getResult();
                     $generardatos = array();
          				foreach($query2 as $entity){
          					 $ctaid= $entity->getCuentaId();
                     $dmcilio= $entity->getDomicilio();
                     $comentario= $entity->getComentarios();
                     $status= $entity->getStatus();
                     $localidad['cuentaid']  = $ctaid;
                     $localidad['domicilio']  = $dmcilio;
                     $localidad['comentarios']  = $comentario;
                     $localidad['status']  = $status;
                     $generardatos[] = $localidad;
				          }
				return new JsonResponse($generardatos);
			}
			public function modificarctaAction(Request $request)
    			{
				$cuentaid = $_POST['ctaid'];
				$domicilio = $_POST['domicilio'];
				$status = $_POST['status'];
				$comentarios = $_POST['comentarios'];
        $cuentaact = $_POST['ctaact'];
             $clienteid = $_POST['clienteid'];
             $dtcliente = $_POST['dtcliente'];
              



              




$manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
$id= $conn->query("SELECT id FROM cuentas_cliente  where cuenta_id='$cuentaid' and cliente='$dtcliente' ")->fetchAll();  
        $idct=$id[0]['id'];
echo $idct;
      

        //   $em = $this->getDoctrine()->getManager();
        //             $queryc = $em->createQuery(
        //             'SELECT p
        //             FROM GestionBundle:CuentasCliente p   
        //             WHERE p.cuentaId = :price '
        //             )->setParameter('price', 'Cholula');
        //              $query2 = $queryc->getResult();
        //              $generardatos = array();
        // foreach($query2 as $entity){
        //    $cuentaid2= $entity->getId();
        //   }
				$cadena= $conn->query("UPDATE cuentas_cliente SET cuenta_id='$cuentaid', domicilio='$domicilio',status='$status', comentarios='$comentarios' WHERE id=$idct and cliente=$dtcliente");
			}

      public function modfclienteAction(Request $request)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
        $cliente = $_POST['cliente'];
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $cadena= $conn->query("UPDATE clientes SET restringir='Restringido',razon_restriccion='El Cliente no Posee Cuenta Registrada' WHERE cliente=$cliente");
      }
      public function modificardetallesAction(Request $request)
      {
        $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
        $cliente = $_POST['cliente'];
        $razon = $_POST['razon'];
        $fiscal = $_POST['fiscal'];
        $ciudad = $_POST['ciudad'];
        $comentarios = $_POST['comentarios'];
        $correo = $_POST['correo'];
        $t1 = $_POST['tlf1'];
        $t2 = $_POST['tlf2'];
        $rest = $_POST['rest'];
        $rfc = $_POST['rfc'];
        $razonrestriccion = ($_POST['razonrestriccion']);
        $manager = $this->getDoctrine()->getManager();
        $conn = $manager->getConnection();
        $cadena= $conn->query("UPDATE clientes SET domicilio_fiscal='$fiscal', comentarios='$comentarios',ciudad='$ciudad', correo='$correo', telefono='$t1',telefono_2='$t2' ,restringir='$rest',razon_social='$razon',rfc='$rfc',razon_restriccion='$razonrestriccion' WHERE cliente=$cliente");
      }

      public function buscarcientesAction(Request $request)
    {
      $session = $request->getSession(); 
        if(!$session->get("usuarionombre")){
            $this->get('session')->getFlashBag()->add('fall','ES NECESARIO INICIAR SESSION');
            return $this->redirect($this->generateUrl('usuario_login'));
        }
 
    $clientes = $this->getDoctrine()->getRepository('GestionBundle:Clientes')->findOneBy(array("razonSocial"=>$_POST["nombre"])); 

      if($clientes){
          $statusobra = true;
      }else{
          $statusobra = false;
      }

        return new JsonResponse($statusobra); 


         
    }

		}
