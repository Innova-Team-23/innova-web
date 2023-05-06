<?php

namespace App\Controller;

use App\Entity\Dream;
use App\Entity\Prediction;
use App\Repository\PredictionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PredictionController extends AbstractController
{
    #[Route('/prediction', name: 'app_prediction')]
    public function index(): Response
    {
        return $this->render('prediction/index.html.twig', [
            'controller_name' => 'PredictionController',
        ]);
    }

    #[Route('/api/predictions', name: 'predictions', methods: ['GET'])]
    public function getDreamList(PredictionRepository $predictRepository, SerializerInterface $serializer): JsonResponse
    {
        $predictList = $predictRepository->findAll();
        $jsonList = $serializer->serialize($predictList, 'json', ['groups' => 'getPredicts']);
        return new JsonResponse($jsonList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/predictions/{id}', name: 'detailPrediction', methods: ['GET'])]
    public function getDetailDream(Prediction $pre, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($pre, 'json', ['groups' => 'getPredicts']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/predictions', name: "createPrediction", methods: ['POST'])]
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {

        $prediction = $serializer->deserialize($request->getContent(), Prediction::class, 'json');

        // On vérifie les erreurs
        $errors = $validator->validate($prediction);


        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $dream = $em->getRepository(Dream::class)->find($prediction->getIdDream());
        if (!$dream) {
            throw new NotFoundHttpException('Dream not found');
        }
        $prediction->setDream($dream);


        $em->persist($prediction);
        $em->flush();

        $json = $serializer->serialize($prediction, 'json', ['groups' => 'getPredicts']);     
        $location = $urlGenerator->generate('detailPrediction', ['id' => $prediction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($json, Response::HTTP_CREATED, ["Location" => $location], true);

    }
}
