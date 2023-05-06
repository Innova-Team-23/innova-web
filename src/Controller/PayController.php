<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Repository\PaysRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PayController extends AbstractController
{
    
    #[Route('/pay', name: 'app_pay')]
    public function index(): Response
    {
        return $this->render('pay/index.html.twig', [
            'controller_name' => 'PayController',
        ]);
    }


    #[Route('/api/pays', name: 'pays', methods: ['GET'])]
    public function getPayList(PaysRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $list = $repository->findAll();
        $jsonList = $serializer->serialize($list, 'json');
        return new JsonResponse($jsonList, Response::HTTP_OK, [], true);
    }

}
