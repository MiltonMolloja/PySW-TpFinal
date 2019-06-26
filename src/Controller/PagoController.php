<?php

namespace App\Controller;

use App\Entity\Pago;
use App\Form\PagoType;
use App\Repository\PagoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*AGREGADOs*/
use Symfony\Component\Serializer\Serializer; 
use Symfony\Component\Serializer\Encoder\JsonEncoder; 
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer; 
/*AGREGADOs*/

/**
 * @Route("/pago")
 */
class PagoController extends AbstractController
{
    /**
     * @Route("/", name="pago_index", methods={"GET"})
     */
    public function index(PagoRepository $pagoRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $pagos = $em->getRepository('App:Pago')->findAll();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $response = new Response();
        $response->setContent($serializer->serialize($pagos, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/new", name="pago_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
       //recupero atributos
       $data = json_decode($request->getContent(), true);
       $pago = new Pago();
       $pago->setImporte($data['importe']);        
       $pago->setEstado($data['estado']);
       $fecha = new \DateTime($data['fecha']);
       $pago->setFecha($fecha);
       
       //Se Modifico El controlador para el Alta de Entidad Moneda  Sin Cliente
       //$em = $this->getDoctrine()->getManager();

       $escribanoArray= $data['escribano'];
       $idEscribano = $escribanoArray['id'];
       $em = $this->getDoctrine()->getManager();
       $escribano = $em->getRepository("App:Escribano")->find($idEscribano);
       $pago->setEscribano($escribano);

       $em->persist($pago);
       $em->flush();

       $result['status'] = 'ok';
       return new Response(json_encode($result), 200);
    }

    /**
     * @Route("/{id}", name="pago_show", methods={"GET"})
     */
    public function show(Pago $pago): Response
    {
        return $this->render('pago/show.html.twig', [
            'pago' => $pago,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pago_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $pago = $em->getRepository('App:Pago')->find($id);
        $pago->setImporte($data['importe']);        
        $pago->setEstado($data['estado']);
        $fecha = new \DateTime($data['fecha']);
        $pago->setFecha($fecha);
        
        //recupero la entidad empresa de la BD que se corresponde con la id
        //que se recibe en formato JSON y le asigno a la propiedad empresa de mensaje.
        $escribanoArray= $data['escribano'];
        $idEscribano = $escribanoArray['id'];
        //$em = $this->getDoctrine()->getManager();
        $escribano = $em->getRepository("App:Escribano")->find($idEscribano);
        $pago->setEscribano($Escribano);
        
        //guardo en la BD la entidad mensaje modificada.
        $em->persist($pago);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
    }

    /**
     * @Route("/{id}", name="pago_delete", methods={"DELETE"})
     */
    public function delete($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $pago = $em->getRepository('App:Pago')->find($id);
        if (!$pago){
            throw $this->createNotFoundException('id incorrecta');
        }
        $em->remove($pago);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
    }
}
