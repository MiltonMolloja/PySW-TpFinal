<?php

namespace App\Controller;

use App\Entity\Novedad;
use App\Form\NovedadType;
use App\Repository\NovedadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/novedad")
 */
class NovedadController extends AbstractController
{
    /**
     * @Route("/", name="novedad_index", methods={"GET"})
     */
    public function index(NovedadRepository $novedadRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $novedades = $em->getRepository('App:Novedad')->findAll();
        $encoders = array(new JsonEncoder());
        $normalizers = array((new ObjectNormalizer())->setIgnoredAttributes(
            [
            "__initializer__",
            "__cloner__",
            "__isInitialized__"
            ]));
        $serializer = new Serializer($normalizers, $encoders);
        $response = new Response();
        $response->setContent($serializer->serialize($novedades, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        /*return $this->render('novedad/index.html.twig', [
            'novedads' => $novedadRepository->findAll(),
        ]);*/
    }

    /**
     * @Route("/new", name="novedad_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $novedad = new Novedad();

        $novedad->setAsunto($data['asunto']);
        $novedad->setMensaje($data['mensaje']);
        $novedad->setEstado($data['estado']);
        $fecha = new \DateTime($data['fecha']);
        $novedad->setFecha($fecha);

        $escribanoArray = $data['escribano'];
        $idEscribano = $escribanoArray['id'];
        $em = $this->getDoctrine()->getManager();
        $escribano = $em->getRepository("App:Escribano")->find($idEscribano);
        $novedad->setEscribano($escribano);

        $em->persist($novedad);
        $em->flush();

        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);

        /*$novedad = new Novedad();
        $form = $this->createForm(NovedadType::class, $novedad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($novedad);
            $entityManager->flush();

            return $this->redirectToRoute('novedad_index');
        }

        return $this->render('novedad/new.html.twig', [
            'novedad' => $novedad,
            'form' => $form->createView(),
        ]);*/
    }

    /**
     * @Route("/{id}", name="novedad_show", methods={"GET"})
     */
    public function show(Novedad $novedad): Response
    {
        return $this->render('novedad/show.html.twig', [
            'novedad' => $novedad,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="novedad_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $novedad = $em->getRepository('App:Novedad')->find($id);

        $novedad->setAsunto($data['asunto']);
        $novedad->setMensaje($data['mensaje']);
        $novedad->setEstado($data['estado']);
        $fecha = new \DateTime($data['fecha']);
        $novedad->setFecha($fecha);

        //recupero la entidad empresa de la BD que se corresponde con la id
        //que se recibe en formato JSON y le asigno a la propiedad empresa de mensaje.
        $escribanoArray = $data['escribano'];
        $idEscribano = $escribanoArray['id'];
        $escribano = $em->getRepository("App:Escribano")->find($idEscribano);
        $novedad->setEscribano($escribano);

        //guardo en la BD la entidad novedad modificada.
        $em->persist($novedad);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);

        /*$form = $this->createForm(NovedadType::class, $novedad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('novedad_index', [
                'id' => $novedad->getId(),
            ]);
        }

        return $this->render('novedad/edit.html.twig', [
            'novedad' => $novedad,
            'form' => $form->createView(),
        ]);*/
    }

    /**
     * @Route("/{id}", name="novedad_delete", methods={"DELETE"})
     */
    public function delete($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $novedad = $em->getRepository('App:Novedad')->find($id);
        if (!$novedad) {
            throw $this->createNotFoundException('id incorrecta');
        }
        $em->remove($novedad);
        $em->flush();
        $result['status'] = 'ok';
        return new Response(json_encode($result), 200);
        /*if ($this->isCsrfTokenValid('delete' . $novedad->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($novedad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('novedad_index');*/
    }
}
