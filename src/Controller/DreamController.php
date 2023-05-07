<?php

namespace App\Controller;

use App\Entity\Dream;
use App\Entity\Publicite;
use App\Entity\User;
use App\Entity\UserPublicite;
use App\Repository\DreamRepository;
use App\Repository\PubliciteRepository;
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

class DreamController extends AbstractController
{
    #[Route('/dream', name: 'app_dream')]
    public function index(): Response
    {
        return $this->render('dream/index.html.twig', [
            'controller_name' => 'DreamController',
        ]);
    }

    #[Route('/api/dreams', name: 'dreams', methods: ['GET'])]
    public function getDreamList(DreamRepository $dreamRepository, SerializerInterface $serializer): JsonResponse
    {
        $dreamList = $dreamRepository->findAll();
        $jsonBookList = $serializer->serialize($dreamList, 'json', ['groups' => 'getDreams']);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/dreams/{id}', name: 'detailDream', methods: ['GET'])]
    public function getDetailDream(Dream $dream, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($dream, 'json', ['groups' => 'getDreams']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/dreams', name: "createDream", methods: ['POST'])]
    public function createDream(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        PubliciteRepository $publiciteRepository
    ): JsonResponse {

        $dream = $serializer->deserialize($request->getContent(), Dream::class, 'json');



        // On vérifie les erreurs
        $errors = $validator->validate($dream);


        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $user = $em->getRepository(User::class)->find($dream->getIdUser());
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }
        $dream->setUser($user);


        $em->persist($dream);
        $em->flush();

        $jsonUser = $serializer->serialize($dream, 'json', ['groups' => 'getDreams']);
        $location = $urlGenerator->generate('detailDream', ['id' => $dream->getId()], UrlGeneratorInterface::ABSOLUTE_URL);


        $isVer =  $this->verifyNombreCauchemar($em, $dream->getIdUser());
        if ($isVer) {

            $userPub  = new UserPublicite();
            $userPub->setUser($user);
            $publiciteRepository = $em->getRepository(Publicite::class);

            $publicites = $publiciteRepository->createQueryBuilder('p')
                ->select('p.id')
                ->getQuery()
                ->getResult();
            
            $publiciteIds = array();
            
            foreach ($publicites as $publicite) {
                $publiciteIds[] = $publicite['id'];
            }
            
            $random_key = array_rand($publiciteIds);
            $random_element = $publiciteIds[$random_key];
            $publicite = $em->getRepository(Publicite::class)->find($random_element);
            $userPub->setPublicite($publicite);
            $em->persist($userPub);
            $em->flush();
        }

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    public function verifyNombreCauchemar(EntityManagerInterface $entityManager, $userId)
    {
        $qb = $entityManager->createQueryBuilder();
        $qb->select('count(r.id)')
            ->from(Dream::class, 'r')
            ->where('r.user = :id_users')
            ->andWhere('r.type = :cauchemar')
            ->andWhere('r.user > 2')
            ->setParameter('id_users', $userId)
            ->setParameter('cauchemar', 'cauchemar');

        $count = $qb->getQuery()->getSingleScalarResult();
        return $count > 0;
    }
}
