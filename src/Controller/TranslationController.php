<?php

namespace App\Controller;

use App\Entity\Translation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

class TranslationController extends AbstractController
{

    private $translator;
    private $entityManager;

    public function __construct(TranslatorInterface $translator,EntityManagerInterface  $entityManager)
    {
        $this->translator = $translator;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/translation', name: "translation", methods: ['GET'])]
    public function translation(Request $request): JsonResponse
    {

        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);
        $langage = $data['langage'];
        
        $translations = $this->entityManager->getRepository(Translation::class)
            ->createQueryBuilder('t')
            ->innerJoin('t.translationTexts', 'tt')
            ->where('tt.language = :lang')
            ->setParameter('lang', $langage)
            ->getQuery()
            ->getResult();

        $result = array();
        foreach ($translations as $translation) {
            $result[$translation->getKey()] = $translation->getTextForLanguage($langage);
        }

        return new JsonResponse($result);

        /*
        $translated = $translator->trans('Hello', [], null, 'fr');
        return new JsonResponse($translated);
        */
    }
}
