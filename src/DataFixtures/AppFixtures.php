<?php

namespace App\DataFixtures;

use App\Entity\Pays;
use App\Entity\Publicite;
use App\Entity\Translation;
use App\Entity\TranslationText;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un user "normal"
        $user = new User();
        $user->setEmail("user@gmail.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $manager->persist($user);

        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setEmail("admin@gmail.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);


        //Creation pay
        $pay = new Pays();
        $pay->setLabel("Madagascar");
        $manager->persist($pay);
        $pay = new Pays();
        $pay->setLabel("France");
        $manager->persist($pay);
        $manager->flush();

        $this->translation($manager);
        $this->publicite($manager);
    }

    public function translation(ObjectManager $entityManager)
    {
        $data =
            [
                "menu1" => [
                    "fr" => "Accueil",
                    "en" => "Home"
                ],
                "auv" => [
                    "fr" => "Aurevoir",
                    "en" => "Good Bye",
                ]

            ];

        foreach ($data as $key => $translations) {
            $translation = new Translation();
            $translation->setKey($key);
            $entityManager->persist($translation);
            $entityManager->flush();
            foreach ($translations as $language => $text) {
                $translationText = new TranslationText();
                $translationText->setTranslation($translation);
                $translationText->setLanguage($language);
                $translationText->setText($text);
                $entityManager->persist($translationText);
                $entityManager->flush();
            }
        }
    }

    public function publicite(ObjectManager $entityManager){
        $tableau_publicite = array(
            array(
                "titre" => "Besoin d'aide pour surmonter vos cauchemars ?",
                "description" => "Onirix peut vous aider à comprendre les causes de vos cauchemars et à les surmonter.",
                "image" => "lien_de_l'image_1.jpg",
                "cta" => "Obtenir de l'aide maintenant"
            ),
            array(
                "titre" => "Vos cauchemars vous empêchent de dormir ?",
                "description" => "Découvrez comment Onirix peut vous aider à réduire vos cauchemars et à retrouver un sommeil réparateur.",
                "image" => "lien_de_l'image_2.jpg",
                "cta" => "En savoir plus"
            ),
            array(
                "titre" => "Les cauchemars affectent votre qualité de vie ?",
                "description" => "Obtenez une analyse détaillée de vos cauchemars avec Onirix et découvrez comment vous pouvez améliorer votre vie.",
                "image" => "lien_de_l'image_3.jpg",
                "cta" => "Commencer maintenant"
            ),
            array(
                "titre" => "Ne laissez pas les cauchemars vous dominer",
                "description" => "Utilisez Onirix pour mieux comprendre vos cauchemars et les surmonter.",
                "image" => "lien_de_l'image_4.jpg",
                "cta" => "Réclamer votre analyse gratuite"
            )
        );

        foreach ($tableau_publicite as $publicite) {
            $entitePublicite = new Publicite();
            $entitePublicite->setTitre($publicite['titre']);
            $entitePublicite->setDescription($publicite['description']);
            $entitePublicite->setImage($publicite['image']);
            $entitePublicite->setCta($publicite['cta']);       
            $entityManager->persist($entitePublicite);
        }
        
        $entityManager->flush();


    }
}
