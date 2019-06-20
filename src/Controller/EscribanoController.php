<?php
namespace App\Controller;
use App\Entity\Escribano;
use App\Form\EscribanoType;
use App\Repository\EscribanoRepository;
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
 * @Route("/escribano")
 */
class EscribanoController extends AbstractController
{
    /**
     * @Route("/", name="escribano_index", methods={"GET"})
     */
    public function index(EscribanoRepository $escribanoRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $escribano = $em->getRepository('App:Escribano')->findAll();
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $response = new Response();
        $response->setContent($serializer->serialize($escribano, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Route("/new", name="escribano_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
         //recupero atributos
         $data = json_decode($request->getContent(), true);
         $escribano = new Escribano();
         $escribano->setMatricula($data['matricula']);        
         $escribano->setUniversidad($data['universidad']);
         $escribano->setEstado($data['estado']);
         
 
         //Se Modifico El controlador para el Alta de Entidad Moneda  Sin Cliente
         //$em = $this->getDoctrine()->getManager();
 
         $escribaniaArray= $data['escribania'];
         $idEscribania = $escribaniaArray['id'];
         $em = $this->getDoctrine()->getManager();
         $escribania = $em->getRepository("App:Escribania")->find($idEscribania);
         $escribano->setEscribania($escribania);
 
 
         $em->persist($escribano);
         $em->flush();
 
 
         $result['status'] = 'ok';
         return new Response(json_encode($result), 200);
 
    }
    /**
     * @Route("/{id}", name="escribano_show", methods={"GET"})
     */
    public function show(Escribano $escribano): Response
    {
        return $this->render('escribano/show.html.twig', [
            'escribano' => $escribano,
        ]);
    }
    /**
     * @Route("/{id}/edit", name="escribano_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $escribano = $em->getRepository('App:Escribano')->find($id);
        $escribano->setMatricula($data['matricula']);        
        $escribano->setUniversidad($data['universidad']);
        $escribano->setEstado($data['estado']);
        
        //recupero la entidad empresa de la BD que se corresponde con la id
        //que se recibe en formato JSON y le asigno a la propiedad empresa de mensaje.
        $escribaniaArray= $data['escribania'];
        $idEscribania = $escribaniaArray['id'];
        //$em = $this->getDoctrine()->getManager();
        $escribania = $em->getRepository("App:Escribania")->find($idEscribania);
        $escribano->setEscribania($escribania);
        
        //guardo en la BD la entidad mensaje modificada.
        $em->persist($escribano);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
    }
    /**
     * @Route("/{id}", name="escribano_delete", methods={"DELETE"})
     */
    public function delete($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $escribano = $em->getRepository('App:Escribano')->find($id);
        if (!$escribano){
            throw $this->createNotFoundException('id incorrecta');
        }
        $em->remove($escribano);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
    }    
}
