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
    private $recettes;

    public function __construct(Security $security, CategoriesRepository $categorie, RecettesRepository $recette)
    {
        $this->categories = $categorie;
        $this->recettes = $recette;
        $this->security = $security;
    }

    /**
     * @Route("/", name="recettes_index", methods={"GET"})
     */
    public function index(): Response
    {
        if($this->security->getUser()){
            if(!$this->security->getUser()->IsVerified()){
                $this->addFlash('error','Votre compte n\'est pas vérifié, vous ne pouvez pas éditer de recette');
            }
        }
        
        return $this->render('recettes/index.html.twig', [
            'recettes' => $this->recettes->findAll(),
            'categories' => $this->categories->findAll()

        ]);
    }

    /**
    * @Route("/mesrecettes", name="mes_recettes", methods={"GET"})
    */
    public function mesrecettes(): Response
    {

        return $this->render('recettes/mes_recettes.html.twig', [
            'recettes' => $this->recettes->findMyRec($this->getUser()->getPseudo()),
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
             //recup de l'image obligatoire
             $image = $form->get('image')->getData();
             $images = $form->get('images')->getData();
 
             $fichier = md5(uniqid()) . '.' . $image->guessExtension();
             $image->move(
                 $this->getParameter('images_directory'),
                 $fichier
             );
             //recup des images complementaire non obligatoire
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
            $recette->setEditor($this->getUser()->getPseudo());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recette);
            $entityManager->flush();

            return $this->redirectToRoute('recettes_index');
        }

        return $this->render('recettes/new.html.twig', [
            'recette' => $recette,
            'categories' => $this->categories->findAll(),
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
    
dd($recette);

        $form = $this->createForm(RecettesType::class, $recette);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recettes_index');
        }

        return $this->render('recettes/edit.html.twig', [
            'recette' => $recette,
            'categories' => $this->categories->findAll(),
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

    /**
     * @Route("/categorie/{id}", name="recettes_categorie")
     */
    public function showCategorie( $id)
    {
        $this->recettes = $this->categories->find($id);

        return $this->render('recettes/index.html.twig', [
            'recettes' => $this->recettes->getRecettes(),
            'categories' => $this->categories->findAll()
        ]);
 
    }
}
