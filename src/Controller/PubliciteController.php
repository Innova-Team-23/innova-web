<?php

namespace App\Controller;

use App\Entity\Publicite;
use App\Repository\PubliciteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PubliciteController extends AbstractController
{
    #[Route('/publicite', name: 'app_publicite')]
    public function index(): Response
    {
        return $this->render('publicite/index.html.twig', [
            'controller_name' => 'PubliciteController',
        ]);
    }

    #[Route('/api/publicites', name: 'publicites', methods: ['GET'])]
    public function getUserList(PubliciteRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $list = $repository->findAll();
        $jsonList = $serializer->serialize($list, 'json', ['groups' => 'getPublicite']);
        return new JsonResponse($jsonList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/publicites/{id}', name: 'detailPublicite', methods: ['GET'])]
    public function getDetailPublicite(Publicite $pub, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($pub, 'json', ['groups' => 'getPublicite']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/publicites', name: "createPublicite", methods: ['POST'])]
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
        $pub = new Publicite();
        $pub->setTitre($request->request->get('titre'));
        $pub->setDescription($request->request->get('description'));
        $pub->setCta($request->request->get('cta'));
     

        $imageFile = $request->files->get('image');

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Erreur lors du téléchargement du fichier
            }
            $pub->setImage("uploads/".$newFilename);
        }
        // Valider les données
        $errors = $validator->validate($pub);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($pub);
        $em->flush();

        $json = $serializer->serialize($pub, 'json', ['groups' => 'getPublicite']);
        $location = $urlGenerator->generate('detailPublicite', ['id' => $pub->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($json, Response::HTTP_CREATED, ["Location" => $location], true);
    }

}
