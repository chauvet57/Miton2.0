<?php

namespace App\Controller;

use App\Entity\CategorieAliment;
use App\Form\CategorieAlimentType;
use App\Repository\CategorieAlimentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorie/aliment")
 */
class CategorieAlimentController extends AbstractController
{
    /**
     * @Route("/", name="categorie_aliment_index", methods={"GET"})
     */
    public function index(CategorieAlimentRepository $categorieAlimentRepository): Response
    {
        return $this->render('categorie_aliment/index.html.twig', [
            'categorie_aliments' => $categorieAlimentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="categorie_aliment_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $categorieAliment = new CategorieAliment();
        $form = $this->createForm(CategorieAlimentType::class, $categorieAliment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorieAliment);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_aliment_index');
        }

        return $this->render('categorie_aliment/new.html.twig', [
            'categorie_aliment' => $categorieAliment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_aliment_show", methods={"GET"})
     */
    public function show(CategorieAliment $categorieAliment): Response
    {
        return $this->render('categorie_aliment/show.html.twig', [
            'categorie_aliment' => $categorieAliment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categorie_aliment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CategorieAliment $categorieAliment): Response
    {
        $form = $this->createForm(CategorieAlimentType::class, $categorieAliment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_aliment_index');
        }

        return $this->render('categorie_aliment/edit.html.twig', [
            'categorie_aliment' => $categorieAliment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_aliment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CategorieAliment $categorieAliment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieAliment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorieAliment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_aliment_index');
    }
}
