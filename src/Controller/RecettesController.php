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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $this->categories = $categorie;
        $this->recettes = $recette;
        $this->security = $security;
        $this->s = $serializer; 
    }

    /**
     * @Route("/", name="recettes_index", methods={"GET"})
     */
    public function index(): Response
    {
        if($this->security->getUser()){
            if(!$this->security->getUser()->IsVerified()){
                $this->addFlash('error','Votre compte n\'est pas vérifié (regarder votre boite email), vous ne pouvez pas éditer de recette');
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

                $tabTemp['idCategorie'] = $dataIng[$i]['categorie_aliment']->getId();
                $tabTemp['categorie'] = $dataIng[$i]['categorie_aliment']->getNomCategorieAliment();
                $tabTemp['idAliment'] = $dataIng[$i]['aliment']->getId();
                $tabTemp['aliment'] = $dataIng[$i]['aliment']->getNomAliment();
                $tabTemp['ingredient'] = $dataIng[$i]['ingredient'];
                $tabTemp['quantite'] = $dataIng[$i]['quantite'];
                $tabTemp['unite'] = $dataIng[$i]['unite']->getNomUnite();
            
                array_push($tabIng,$tabTemp);
            }

            //recuperation des etapes
            $dataEta = $form->get('etape')->getViewData();
            $tabEta = array();
            $tabTempEta = array();
            $c = 0;

            for ($i=0; $i < count($dataEta)+$c ; $i++) { 
                $tabTempEta = array();
                
                    if(isset($dataEta[$i])){
                        $c ++;
                        $tabTempEta['etape'] = $dataEta[$i];
                        array_push($tabEta, $tabTempEta);
                    }
                }
  
            //mise en forme de notre recette pour la Db
            $recette->setValide(false);
            $recette->setTemps($this->s->serialize($form->get('temps')->getViewData(), 'json'));
            $recette->setIngredient($this->s->serialize($tabIng, 'json'));
            $recette->setImage($fichier);
            $recette->setImages($this->s->serialize($arImg, 'json'));
            $recette->setEtape($this->s->serialize($tabEta, 'json'));
            $recette->setEditor($this->getUser()->getPseudo());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recette);
            $entityManager->flush();

            $this->addFlash('success','Votre recette a bien été enregistrée, elle sera visible après validation');
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
