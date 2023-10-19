<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category->setName('Actualités');
        $manager->persist($category);

        $category2 = new Category();
        $category2->setName('Santé et bien-être');
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setName('Art et culture');
        $manager->persist($category3);

        $category4 = new Category();
        $category4->setName('Voyages et tourisme');
        $manager->persist($category4);

        $category5 = new Category();
        $category5->setName('Sciences et technologie');
        $manager->persist($category5);

        $article = new Article();
        $article->setTitle("La révolution de l'intelligence artificielle dans l'industrie")
            ->setContent("L'intelligence artificielle (IA) a transformé l'industrie de manière spectaculaire au cours des dernières années. Elle a permis d'automatiser des processus complexes, d'analyser d'énormes ensembles de données et de créer des solutions novatrices dans divers secteurs...")
            ->setAuthor("John Doe")
            ->setCreatedAt(new \DateTime("2023-05-10"))
            ->setUpdatedAt(new \DateTime("2023-06-15"))
            ->setNbViews(1200)
            ->setPublished(TRUE);
        $manager->persist($article);

        $article2 = new Article();
        $article2->setTitle("L'influence de l'impressionnisme sur l'art moderne")
            ->setContent("L'impressionnisme, mouvement artistique révolutionnaire du XIXe siècle, a profondément influencé l'art moderne. Ses techniques novatrices de représentation de la lumière et de la nature ont ouvert la voie à de nouvelles formes d'expression artistique, marquant ainsi un tournant crucial dans l'histoire de l'art...")
            ->setAuthor("Sarah Dupont")
            ->setCreatedAt(new \DateTime("2023-09-12"))
            ->setUpdatedAt(new \DateTime("2023-10-25"))
            ->setNbViews(700)
            ->setPublished(TRUE);
        $manager->persist($article2);

        $article3 = new Article();
        $article3->setTitle("Exploration de la renaissance culturelle italienne")
            ->setContent("La Renaissance italienne, période remarquable de renouveau culturel et artistique, a engendré des chefs-d'œuvre qui continuent d'inspirer les artistes du monde entier. De la peinture à la sculpture en passant par l'architecture, cette époque a marqué un point tournant dans l'histoire de l'art occidental...")
            ->setAuthor("Luca Rossi")
            ->setCreatedAt(new \DateTime("2023-01-08"))
            ->setUpdatedAt(new \DateTime("2023-03-20"))
            ->setNbViews(900)
            ->setPublished(TRUE);
        $manager->persist($article3);

        $article4 = new Article();
        $article4->setTitle("Découverte des merveilles cachées de l'Asie du Sud-Est")
            ->setContent("L'Asie du Sud-Est regorge de joyaux cachés qui captivent l'imagination des voyageurs du monde entier. Des plages immaculées aux temples anciens en passant par la cuisine exotique, cette région offre une expérience culturelle et naturelle inoubliable pour les amateurs d'aventure et de découverte...")
            ->setAuthor("Sophie Martin")
            ->setCreatedAt(new \DateTime("2023-05-15"))
            ->setUpdatedAt(new \DateTime("2023-07-25"))
            ->setNbViews(1200)
            ->setPublished(TRUE);
        $manager->persist($article4);

        $article5 = new Article();
        $article5->setTitle("Exploration des trésors naturels de l'Amérique du Sud")
            ->setContent("L'Amérique du Sud offre une diversité naturelle extraordinaire, allant des majestueuses montagnes des Andes aux profondeurs mystérieuses de la forêt amazonienne. Avec une faune et une flore uniques, cette région est un paradis pour les amoureux de la nature et les passionnés d'aventure en quête d'expériences inoubliables...")
            ->setAuthor("Carlos Hernandez")
            ->setCreatedAt(new \DateTime("2023-07-12"))
            ->setUpdatedAt(new \DateTime("2023-09-05"))
            ->setNbViews(1500)
            ->setPublished(TRUE);
        $manager->persist($article5);



        $comment = new Comment();
        $comment->setTitle("Une analyse concise")->setArticle($article2)->setAuthor("Marie Claire")->setCreatedAt(new \DateTime("2023-10-15"))->setMessage( "Cet article met en évidence la magie de l'impressionnisme de manière concise et informative. Merci pour cette analyse approfondie !");
        $manager->persist($comment);

        $comment2 = new Comment();
        $comment2->setTitle("Réflexions captivantes sur la révolution de la lumière")->setArticle($article2)->setAuthor("Pierre Dubois")->setCreatedAt(new \DateTime("2023-10-17"))->setMessage( "L'impressionnisme a révolutionné la manière dont nous percevons la lumière et la couleur. C'est un article captivant qui fait ressortir toute l'importance de ce mouvement artistique.");
        $manager->persist($comment2);

        $comment3 = new Comment();
        $comment3->setTitle("L'IA réinvente nos industries")->setArticle($article)->setAuthor("Alice Lefebvre")->setCreatedAt(new \DateTime("2023-06-20"))->setMessage( "Cet article met en évidence l'impact réel de l'intelligence artificielle sur nos processus industriels. J'apprécie la façon dont il explore en détail les avantages et les défis de cette révolution.");
        $manager->persist($comment3);

        $comment4 = new Comment();
        $comment4->setTitle("Une aventure inoubliable en Amérique du Sud")
            ->setArticle($article5)
            ->setAuthor("Elena Rodriguez")
            ->setCreatedAt(new \DateTime("2023-07-20"))
            ->setMessage("Cet article capture parfaitement la beauté et la diversité incroyable de l'Amérique du Sud. Après avoir lu cet article, je ne peux qu'aspirer à partir à l'aventure et à découvrir ces trésors naturels par moi-même.");
        $manager->persist($comment4);
        
        $manager->flush();
    }
}
