<?php

namespace App\Controller;

use App\Entity\Commentaires;
use App\Entity\Recettes;
use App\Form\CommentairesType;
use App\Form\RecettesType;
use App\Repository\CategoriesRepository;
use App\Repository\RecettesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/recettes")
 */
class RecettesController extends AbstractController
{


    private $security;
    private $categories;

    public function __construct(Security $security, CategoriesRepository $categorie)
    {
        $this->categories = $categorie;
        $this->security = $security;
    }

    /**
     * @Route("/", name="recettes_index", methods={"GET"})
     */
    public function index(RecettesRepository $recettesRepository): Response
    {
        return $this->render('recettes/index.html.twig', [
            'recettes' => $recettesRepository->findAll(),
            'categories' => $this->categories->findAll()

        ]);
    }

    /**
     * @Route("/new", name="recettes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $recette = new Recettes();
        $form = $this->createForm(RecettesType::class, $recette);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

             $arImg = array();
             //recup image
             $image = $form->get('image')->getData();
             $images = $form->get('images')->getData();
 
             $fichier = md5(uniqid()) . '.' . $image->guessExtension();
             $image->move(
                 $this->getParameter('images_directory'),
                 $fichier
             );
             //recup images
             foreach ($images as $img){
                     $fic = md5(uniqid()) . '.' . $img->guessExtension();
                     $img->move(
                         $this->getParameter('images_directory'),
                         $fic
                     );
                     
              array_push($arImg, $fic);       
             
             }



        //recuperation de notre liste ingredient
            $dataIng = $form->get('ingredient')->getViewData();
            $tabIng = array();
            for ($i=0; $i < count($dataIng) ; $i++) { 
                $tabTemp = array();

                $catIdTemp = $dataIng[$i]['categorie_aliment']->getId();
                $catAlTemp = $dataIng[$i]['categorie_aliment']->getNomCategorieAliment();
                $aliIdTemp = $dataIng[$i]['aliment']->getId();
                $aliTemp = $dataIng[$i]['aliment']->getNomAliment();
                $ingTemp = $dataIng[$i]['ingredient'];
                $quaTemp = $dataIng[$i]['quantite'];
                $uniTemp = $dataIng[$i]['unite']->getNomUnite();

                $tabTemp['idCategorie'] = $catIdTemp;
                $tabTemp['categorie'] = $catAlTemp;
                $tabTemp['idAliment'] = $aliIdTemp;
                $tabTemp['aliment'] = $aliTemp;
                $tabTemp['ingredient'] = $ingTemp;
                $tabTemp['quantite'] = $quaTemp;
                $tabTemp['unite'] = $uniTemp;
            
            array_push($tabIng,$tabTemp);
            }
        
        //mise en forme de notre recette pour la Db
            $recette->setValide(false);
            $recette->setTemps($form->get('temps')->getViewData());
            $recette->setIngredient($tabIng);
            $recette->setImage($fichier);
            $recette->setImages($arImg);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recette);
            $entityManager->flush();

            return $this->redirectToRoute('recettes_index');
        }

        return $this->render('recettes/new.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recettes_show", methods={"GET","POST"})
     */
    public function show(Request $request, Recettes $recette): Response
    {
        //formulaire
        $commentaire = new Commentaires();
        $form = $this->createForm(CommentairesType::class,$commentaire);
        $form -> handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $commentaire->setPseudo($this->security->getUser()->getPseudo());
            $commentaire->setRecette($recette);
           
 
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();
            $this->addFlash('success','Votre commentaire a bien était posté !!');
            return $this->redirectToRoute('recettes_show', array(
                'id' => $recette->getId()) );
        }
//dd($recette);
        return $this->render('recettes/show.html.twig', [
            'recette' => $recette,
            'categories' => $this->categories->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="recettes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Recettes $recette): Response
    {
        $form = $this->createForm(RecettesType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recettes_index');
        }

        return $this->render('recettes/edit.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recettes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Recettes $recette): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('recettes_index');
    }
}
