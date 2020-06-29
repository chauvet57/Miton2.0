<?php

namespace App\Controller;

use App\Entity\Recettes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RecettesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

 

    /**
     * @Route("/api", name="api")
     */
class ApiController extends AbstractController
{
    /**
     * @Route("/liste/recettes", name="api")
     */
    public function index(RecettesRepository $recette,SerializerInterface $serializer)
    {
        $recettes = $recette->findAll();

        for ($i=0; $i < count($recettes); $i++) { 

            $recettes[$i]->setTemps(json_decode($recettes[$i]->getTemps(),'json'));
            $recettes[$i]->setIngredient(json_decode($recettes[$i]->getIngredient(),'json'));
            $recettes[$i]->setImages(json_decode($recettes[$i]->getImages(),'json'));
            $recettes[$i]->setEtape(json_decode($recettes[$i]->getEtape(),'json'));
        }
      
        $data = $serializer->serialize($recettes, 'json', ['groups'=>['list']]);

        return new JsonResponse($data, 200, [], true); 
    }
}
