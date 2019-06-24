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
        $pago = new Pago();
        $form = $this->createForm(PagoType::class, $pago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pago);
            $entityManager->flush();

            return $this->redirectToRoute('pago_index');
        }

        return $this->render('pago/new.html.twig', [
            'pago' => $pago,
            'form' => $form->createView(),
        ]);
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
    public function edit(Request $request, Pago $pago): Response
    {
        $form = $this->createForm(PagoType::class, $pago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pago_index', [
                'id' => $pago->getId(),
            ]);
        }

        return $this->render('pago/edit.html.twig', [
            'pago' => $pago,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pago_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Pago $pago): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pago->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pago);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pago_index');
    }
}
