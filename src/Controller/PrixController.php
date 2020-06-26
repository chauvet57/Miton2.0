<?php

namespace App\Controller;

use App\Entity\Prix;
use App\Form\PrixType;
use App\Repository\PrixRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/prix")
 */
class PrixController extends AbstractController
{
    /**
     * @Route("/", name="prix_index", methods={"GET"})
     */
    public function index(PrixRepository $prixRepository): Response
    {
        return $this->render('prix/index.html.twig', [
            'prixes' => $prixRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="prix_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $prix = new Prix();
        $form = $this->createForm(PrixType::class, $prix);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($prix);
            $entityManager->flush();

            return $this->redirectToRoute('prix_index');
        }

        return $this->render('prix/new.html.twig', [
            'prix' => $prix,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="prix_show", methods={"GET"})
     */
    public function show(Prix $prix): Response
    {
        return $this->render('prix/show.html.twig', [
            'prix' => $prix,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="prix_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Prix $prix): Response
    {
        $form = $this->createForm(PrixType::class, $prix);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('prix_index');
        }

        return $this->render('prix/edit.html.twig', [
            'prix' => $prix,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="prix_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Prix $prix): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prix->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($prix);
            $entityManager->flush();
        }

        return $this->redirectToRoute('prix_index');
    }
}
