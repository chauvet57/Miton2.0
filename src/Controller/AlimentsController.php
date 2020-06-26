<?php

namespace App\Controller;

use App\Entity\Aliments;
use App\Form\AlimentsType;
use App\Repository\AlimentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/aliments")
 */
class AlimentsController extends AbstractController
{
    /**
     * @Route("/", name="aliments_index", methods={"GET"})
     */
    public function index(AlimentsRepository $alimentsRepository): Response
    {
        return $this->render('aliments/index.html.twig', [
            'aliments' => $alimentsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="aliments_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $aliment = new Aliments();
        $form = $this->createForm(AlimentsType::class, $aliment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($aliment);
            $entityManager->flush();

            return $this->redirectToRoute('aliments_index');
        }

        return $this->render('aliments/new.html.twig', [
            'aliment' => $aliment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="aliments_show", methods={"GET"})
     */
    public function show(Aliments $aliment): Response
    {
        return $this->render('aliments/show.html.twig', [
            'aliment' => $aliment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="aliments_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Aliments $aliment): Response
    {
        $form = $this->createForm(AlimentsType::class, $aliment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('aliments_index');
        }

        return $this->render('aliments/edit.html.twig', [
            'aliment' => $aliment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="aliments_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Aliments $aliment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$aliment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($aliment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('aliments_index');
    }
}
