<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
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
 * @Route("/usuario")
 */
class UsuarioController extends AbstractController
{
    /**
     * @Route("/", name="usuario_index", methods={"GET"})
     */
    public function index(UsuarioRepository $usuarioRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository('App:Usuario')->findAll();
        //$usuarios = array('usuarios' => $usuarios);
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $response = new Response();
        $response->setContent($serializer->serialize($usuarios, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/new", name="usuario_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
         //Recuperacion de Atributos
         $data = json_decode($request->getContent(), true);
         $em = $this->getDoctrine()->getManager();       
         $usuario = new Usuario();
         $usuario->setEstado($data['estado']); //Cuando intenta recuperar el estado con data no puede pero si pasas true pasa al siguiente 
         $usuario->setUsername($data['username']);  
         $usuario->setPassword($data['password']);
         $usuario->setEmail($data['email']);
         $usuario->setTipo($data['tipo']);
         $usuario->setImagen($data['imagen']); 

        //Confecciono una entidad Perfil
        $perfilArray= $data['perfil'];
        $idPerfil = $perfilArray['id'];        
        $perfil = $em->getRepository("App:Perfil")->find($idPerfil);
        $usuario->setPerfil($perfil);

        if( $usuario->getTipo() != 'Socio' )
        {
            $usuario->setEscribano(null);
        } 
        else
        {
            $escribanoArray= $data['escribano'];
            $idEscribano = $escribanoArray['id'];        
            $escribano = $em->getRepository("App:Escribano")->find($idEscribano);
            $usuario->setEscribano($escribano);
        }

        $em->persist($usuario);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);

    }

    /**
     * @Route("/{id}", name="usuario_show", methods={"GET"})
     */
    public function show(Usuario $usuario): Response
    {
        return $this->render('usuario/show.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="usuario_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('App:Usuario')->find($id);
        $usuario->setUsername($data['username']);
        $usuario->setPassword($data['password']);
        $usuario->setEmail($data['email']);
        $usuario->setTipo($data['tipo']);
        $usuario->setImagen($data['imagen']);
        $usuario->setEstado($data['estado']); //El estado no se modifica aqui.
       
        //El perfil sigue teniendo el mismo id y los datos ya se modificaron antes.
        
        //Lo que importa es el tipo de escribano
        if( $data['escribano'] == null )
        {
            $usuario->setEscribano(null);
        }
        else
        {
            $escribanoArray= $data['escribano'];
            $idEscribano = $escribanoArray['id'];        
            $escribano = $em->getRepository("App:Escribano")->find($idEscribano);
            $usuario->setEscribano($escribano);
        }

        //Se guarda la entidad modificada.
        $em->persist($usuario);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
    }

    /**
     * @Route("/{id}", name="usuario_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Usuario $usuario): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($usuario);
            $entityManager->flush();
        }

        return $this->redirectToRoute('usuario_index');
    }

    /**
    * @Route("/login", name="usuario_login", methods={"GET","POST"})
    */
    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        //creamos un usuario
        $username = $data['username'];
        $userpassword = $data['password'];
        //creamos un array criteria con los parametros de busqueda de un usuario en la bd
        $criteria = array('username' => $username, 'password' => $userpassword);
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("App:Usuario")->findBy($criteria);
        if($user != null){
            if ($user[0]->getEstado()==true){
            $result['status'] = 'ok';
            $result['username'] = $user[0]->getUsername();
            $result['tipo'] = $user[0]->getTipo();
            $result['imagen'] = $user[0]->getImagen();
            }else{
                $result['status'] = 'bad';
                $result['username'] = '';
                $result['perfil'] = '';
            }
        }else{
            $result['status'] = 'bad';
            $result['username'] = '';
            $result['perfil'] = '';
        }
        return new Response(json_encode($result), 200);
    }

    /**
     * @Route("/{id}/borrado", name="usuario_borrado", methods={"GET","POST"})
     */
    public function borrado($id): Response
    {
        //BORRADO LOGICO: Aqui unicamente se cambiara el estado a 0 (Falso)
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('App:Usuario')->find($id);
        $usuario->setEstado(false);
        
        //Se guarda la entidad modificada.
        $em->persist($usuario);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
    }

    /**
     * @Route("/validacionUsername", name="usuario_username", methods={"GET","POST"})
     */
    public function validarUsername(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $arrayIdUsername = json_decode($request->getContent(), true); //Se recibe el id en la posiccion 0 y el Username en la 1
        //Para la creaccion
        //Pregunta si el id es -1       
        if( $arrayIdUsername[0] == '-1'  )
        {
            //Se esta creando recian
            $usuario = $em->getRepository('App:Usuario')->findBy(['username' => $arrayIdUsername[1] ]); //Se usa el findBy para encontrar el username
            if( $usuario == null )
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
            $usuario = $em->getRepository('App:Usuario')->findBy(['username' => $arrayIdUsername[1] ]); //Se usa el findBy para encontrar el username
            //Si es igual a nulo se trata de un username no registrado
            if( $usuario == null )
            {
                $result = false; //No se encontro
            }
            else
            {
                if( $usuario[0]->getId() == $arrayIdUsername[0] )
                {
                    $result = false; // Sòlo se repite para este usuario
                }
                else
                {
                    $result = true;  // Se repito para otro usuario
                }
            }
        }
        return new Response(json_encode($result), 200);
    }

    /**
     * @Route("/validacionCorreo", name="usuario_correo", methods={"GET","POST"})
     */
    public function validarCorreo(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $arrayIdCorreo = json_decode($request->getContent(), true); //Se recibe el id en la posiccion 0 y el correo en la 1
        //Para la creaccion
        //Pregunta si el id es -1       
        if( $arrayIdCorreo[0] == '-1'  )
        {
            //Se esta creando recian
            $usuario = $em->getRepository('App:Usuario')->findBy(['email' => $arrayIdCorreo[1] ]); //Se usa el findBy para encontrar el correo
            if( $usuario == null )
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
            $usuario = $em->getRepository('App:Usuario')->findBy(['email' => $arrayIdCorreo[1] ]); //Se usa el findBy para encontrar el correo
            //Si es igual a nulo se trata de un email no registrado
            if( $usuario == null )
            {
                $result = false; //No se encontro
            }
            else
            {
                if( $usuario[0]->getId() == $arrayIdCorreo[0] )
                {
                    $result = false; // Sòlo se repite para este usuario
                }
                else
                {
                    $result = true;  // Se repito para otro usuario
                }
            }
        }
        return new Response(json_encode($result), 200);
    }

}

