<?php

namespace App\Controller;

use App\Entity\Escribania;
use App\Form\EscribaniaType;
use App\Repository\EscribaniaRepository;
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
 * @Route("/escribania")
 */
class EscribaniaController extends AbstractController
{
    /**
     * @Route("/", name="escribania_index", methods={"GET"})
     */
    public function index(EscribaniaRepository $escribaniaRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $escribanias = $em->getRepository('App:Escribania')->findAll();
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $response = new Response();
        $response->setContent($serializer->serialize($escribanias, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response; 
    }
    /**
     * @Route("/new", name="escribania_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $escribanium = new Escribania();
        $form = $this->createForm(EscribaniaType::class, $escribanium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($escribanium);
            $entityManager->flush();

            return $this->redirectToRoute('escribania_index');
        }

        return $this->render('escribania/new.html.twig', [
            'escribanium' => $escribanium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="escribania_show", methods={"GET"})
     */
    public function show(Escribania $escribanium): Response
    {
        return $this->render('escribania/show.html.twig', [
            'escribanium' => $escribanium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="escribania_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Escribania $escribanium): Response
    {
        $form = $this->createForm(EscribaniaType::class, $escribanium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('escribania_index', [
                'id' => $escribanium->getId(),
            ]);
        }

        return $this->render('escribania/edit.html.twig', [
            'escribanium' => $escribanium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="escribania_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Escribania $escribanium): Response
    {
        if ($this->isCsrfTokenValid('delete'.$escribanium->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($escribanium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('escribania_index');
    }
}
