<?php

namespace App\Controller;

use App\Entity\Perfil;
use App\Form\PerfilType;
use App\Repository\PerfilRepository;
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
 * @Route("/perfil")
 */
class PerfilController extends AbstractController
{
    /**
     * @Route("/", name="perfil_index", methods={"GET"})
     */
    public function index(PerfilRepository $perfilRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $perfiles = $em->getRepository('App:Perfil')->findAll();
        //$perfiles = array('perfiles' => $perfiles);
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $response = new Response();
        $response->setContent($serializer->serialize($perfiles, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/new", name="perfil_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {    
        //Se recupera los atributos
        $data = json_decode($request->getContent(), true);
        $perfil = new Perfil();
        $perfil->setNombres($data['nombres']);
        $perfil->setApellidos($data['apellidos']);
        $perfil->setDni($data['dni']);
        $perfil->setSexo($data['sexo']);
        $perfil->setEstado($data['estado']);                
        $fecha = new \DateTime($data['fechaNac']);
        $perfil->setFechaNac($fecha);

        $em = $this->getDoctrine()->getManager();
        $em->persist($perfil);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
    }

    /**
     * @Route("/{id}", name="perfil_show", methods={"GET"})
     */
    public function show(Perfil $perfil): Response
    {
        return $this->render('perfil/show.html.twig', [
            'perfil' => $perfil,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="perfil_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request): Response
    {
		$data = json_decode($request->getContent(), true);

        $em = $this->getDoctrine()->getManager();
        $perfil = $em->getRepository('App:Perfil')->find($id);

        $perfil->setNombres($data['nombres']);
        $perfil->setApellidos($data['apellidos']);
        $perfil->setDni($data['dni']);
        $perfil->setSexo($data['sexo']);                
        $fecha = new \DateTime($data['fechaNac']);
        $perfil->setFechaNac($fecha);
        //El estado no es necesario cambiarlos aqui.          

        //$em = $this->getDoctrine()->getManager();
        //guardo en la BD
        $em->persist($perfil);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
    }

    /**
     * @Route("/{id}", name="perfil_delete", methods={"DELETE"})
     */
    public function delete($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $perfil = $em->getRepository('App:Perfil')->find($id);
        if (!$perfil){
            throw $this->createNotFoundException('id incorrecta');
        }
        $em->remove($perfil);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
    }

    /**
     * @Route("/{id}/borrado", name="perfil_borrado", methods={"GET","POST"})
     */
    public function borrado($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $perfil = $em->getRepository('App:Perfil')->find($id);
        $perfil->setEstado(false);                
                  

        //$em = $this->getDoctrine()->getManager();
        //guardo en la BD la entidad mensaje modificada.
        $em->persist($perfil);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
    }

    /**
     * @Route("/validacionDocumento", name="perfil_documento", methods={"GET","POST"})
     */
    public function validarDNI(Request $request):Response
    {
        $em = $this->getDoctrine()->getManager();
        $arrayIdDni = json_decode($request->getContent(), true); //Se recibe el id en la posiccion 0 y el DNI en la 1
        //Para la creaccion
        //Pregunta si el id es -1       
        if( $arrayIdDni[0] == '-1'  )
        {
            //Se esta creando recian
            $escribano = $em->getRepository('App:Perfil')->findBy(['dni' => $arrayIdDni[1] ]); //Se usa el findBy para encontrar el dni
            if( $escribano == null )
            {
                $result = false;  //No se encontro
            }
            else
            { 
                $result = true; //Se encontro
            }
        }
        else
        {
            //Para la modificacion.
            $escribano = $em->getRepository('App:Perfil')->findBy(['dni' => $arrayIdDni[1] ]); //Se usa el findBy para encontrar el dni
            //Si es igual a nulo se trata de un dni no registrado
            if( $escribano == null )
            {
                $result = false; //No se encontro
            }
            else
            {
                if( $escribano[0]->getId() == $arrayIdDni[0] )
                {
                    $result = false; // Sòlo se repite para ese perfil
                }
                else
                {
                    $result = true;  // Se repito para otro perfil
                }
            }
        }
        return new Response(json_encode($result), 200);
    }

}
