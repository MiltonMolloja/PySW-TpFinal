<?php

namespace App\Controller;

use App\Entity\Escribano;
use App\Form\EscribanoType;
use App\Repository\EscribanoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->render('escribano/index.html.twig', [
            'escribanos' => $escribanoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="escribano_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $escribano = new Escribano();
        $form = $this->createForm(EscribanoType::class, $escribano);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($escribano);
            $entityManager->flush();

            return $this->redirectToRoute('escribano_index');
        }

        return $this->render('escribano/new.html.twig', [
            'escribano' => $escribano,
            'form' => $form->createView(),
        ]);
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
    public function edit(Request $request, Escribano $escribano): Response
    {
        $form = $this->createForm(EscribanoType::class, $escribano);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('escribano_index', [
                'id' => $escribano->getId(),
            ]);
        }

        return $this->render('escribano/edit.html.twig', [
            'escribano' => $escribano,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="escribano_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Escribano $escribano): Response
    {
        if ($this->isCsrfTokenValid('delete'.$escribano->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($escribano);
            $entityManager->flush();
        }

        return $this->redirectToRoute('escribano_index');
    }
}
