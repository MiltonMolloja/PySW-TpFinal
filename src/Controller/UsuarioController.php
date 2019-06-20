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
        $usuarios = array('usuarios' => $usuarios);
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $response = new Response();
        $response->setContent($serializer->serialize($usuarios, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

        /*
        return $this->render('usuario/index.html.twig', [
            'usuarios' => $usuarioRepository->findAll(),
        ]);
        */
    }

    /**
     * @Route("/new", name="usuario_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        //Recuperacion de Atributos
        $data = json_decode($request->getContent(), true);
        $usuario = new Usuario();
        $usuario->setUsername($data['username']);
        $usuario->setPassword($data['password']);
        $usuario->setEmail($data['email']);
        $usuario->setTipo($data['tipo']);
        $usuario->setImagen($data['imagen']);
        $usuario->setEstado($data['estado']);
        //Se usara el dni del perfil parA obtener el id del perfil 
        //previamente creado
        $perfilArray = $data['perfil'];
        $dniPerfil = $perfilArray['dni'];
        $em = $this->getDoctrine()->getManager();
        $perfil = $em->getRepository("App:Perfil")->find($dniPerfil);
        $usuario->setPerfil($perfil);

        //Aqui se pregunta por el tipo de usuario
        if( $usuario->getTipo() != 'socio' )
        {
            $usuario->setEscribano(null);
        }
        else
        {   
            //Se recupera en base a la matricula
            $escribanoArray = $data['escribano'];
            $matriculaEscribano = $escribanoArray['matricula'];
            $escribano = $em->getRepository("App:Escribano")->find($matriculaEscribano);
            $usuario->setEscribano($escribano);
        }

        $em->persist($usuario);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);


        /*
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($usuario);
            $entityManager->flush();

            return $this->redirectToRoute('usuario_index');
        }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
        */
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
    public function edit(Request $request, Usuario $usuario): Response
    {
        /*
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('usuario_index', [
                'id' => $usuario->getId(),
            ]);
        }

        return $this->render('usuario/edit.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
        */
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
            $result['status'] = 'ok';
            $result['username'] = $user[0]->getUsername();
            $result['tipo'] = $user[0]->getTipo();
        }else{
            $result['status'] = 'bad';
            $result['username'] = '';
            $result['perfil'] = '';
        }
        return new Response(json_encode($result), 200);
    }


}
