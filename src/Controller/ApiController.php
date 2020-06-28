<?php

namespace App\Controller;

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


        //dd($recettes);
        $data = $serializer->serialize($recettes, 'json', ['groups'=>['list']]);

        return new JsonResponse($data, 200, [], true); 
    }
}
