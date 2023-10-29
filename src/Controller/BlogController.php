<?php

namespace App\Controller;


use App\Entity\Article;
use App\Entity\Category;
use App\Services\SpamFinder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_locale}/blog', name: 'blog_')]
class BlogController extends AbstractController {

    #[Route('/list/{page}', name: 'show_list', requirements: ['page' => '\d+'], defaults: ['page' => 1, "_locale" => "fr_FR"])]
    public function listAction($page, EntityManagerInterface $em, TranslatorInterface $translator): Response {

        if ($page < 1) {
            throw new NotFoundHttpException($translator->trans("La page $page n'existe pas."));
        }

        $articlesPerPage = $this->getParameter('my_parameter');
        $articles = $em->getRepository(Article::class)->findAllWithPaging($page, $articlesPerPage);

        $article = count($articles);
        $nbTotalPages = intval(ceil($article/$articlesPerPage));

        if($page > $nbTotalPages){
            throw $this->createNotFoundException($translator->trans("La page demandée n'existe pas"));
        }
        return $this->render('articles/list.html.twig', ['articles' => $articles, 'page'=>$page, 'totalPages'=>$nbTotalPages]);
    }


    #[Route('/article/{id}',
        name: 'view_article',
        requirements: ['id'=> '\d+'])]
    public function viewAction($id, EntityManagerInterface $em, TranslatorInterface $translator) : Response {
        $article = $em->getRepository(Article::class)
            ->find($id);
        //Vérifie si l'article a été publié
        $published = $article->isPublished();
        $views = $article->getNbViews();

        $incrementViews = ($views+1);

        $article->setNbViews($incrementViews);
        $em->persist($article);
        $em->flush();

        if (($article === null) && ($published ==null) ) {
            throw $this->createNotFoundException($translator->trans("La page demandée n'existe pas"));
        } else {
            return $this->render('articles/view.html.twig', [ 'id'=> $id, 'articles' => $article]);
        }
    }

    //addAction : URL : /blog/article/add. Faire un if principal (tester manuellement avec if(true) et if(false)) :
    // • Si on est en train de valider le formulaire (on verra plus tard comment faire)
    // ⇒ message flash de confirmation + redirection vers la liste
    // • Sinon : affichage du formulaire

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/article/add',
        name: 'add_article')]
    public function addAction(EntityManagerInterface $em, Request $request,  SpamFinder $spamFinder, TranslatorInterface $translator) : Response {

        $article = new Article();
        $article->setPublished(1);
        $article->setCreatedAt(new \DateTime());
        $article->setAuthor('Nadia');
        $article->setNbViews(1);
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('content', TextType::class)
            ->add('categories', EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name', // Remplacez 'name' par le champ approprié de votre entité Category
                'multiple' => true, // Si vous autorisez plusieurs choix
                'expanded' => true, // Si vous voulez que les choix soient affichés comme des cases à cocher
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            //Vérification des spams
            $content = $article->getContent();
            if ($spamFinder->isSpam($content)) {
                $translatedMessage = $translator->trans('Votre contenu est identifié comme spam. Veuillez supprimer tout contenu suspect.');
                $this->addFlash('error', $translatedMessage);
                return $this->redirectToRoute('blog_add_article');
            }

            if ($article->getContent() == $article->getTitle() || $article->getNbViews() < 0){
                return $this->render('articles/add.html.twig',  array('form' => $form->createView()));
            }
            //Traitement formulaire
            $em->persist($article);
            $em->flush();

            $translatedMessageAdd = $translator->trans("L'article a bien été ajouté");

            //Message succès
            $this->addFlash('success',$translatedMessageAdd);
            return $this->redirectToRoute('blog_view_article', ['id'=>$article->getId(), 'message'=> $translatedMessageAdd]);

        } else {

            return $this->render('articles/add.html.twig',  array('form' => $form->createView()));
        }
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/article/edit/{id}',
        name: 'edit_article',
        requirements: ['id'=> '\d+'])
    ]

    public function editAction($id, EntityManagerInterface $em, Request $request, TranslatorInterface $translator) : Response {
        $article = $em->getRepository(Article::class)
            ->find($id);

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('content', TextType::class)
            ->add('categories', EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name', // Remplacez 'name' par le champ approprié de votre entité Category
                'multiple' => true, // Si vous autorisez plusieurs choix
                'expanded' => false, // Si vous voulez que les choix soient affichés comme des cases à cocher
            ])
            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if ($article->getContent() == $article->getTitle() || $article->getNbViews() < 0){
                return $this->render('articles/edit.html.twig',  ['form' => $form->createView(), 'id' => $id]);
            }
            //Traitement de l'édition
            $article->setUpdatedAt(new \DateTime());
            $em->persist($article);
            $em->flush();

            $translatedMessageEdit = $translator->trans("L'article a bien été modifié");
            //Message succès

            $this->addFlash('success',$translatedMessageEdit);
            return $this->redirectToRoute('blog_view_article', [ 'id'=> $id,'message'=> $translatedMessageEdit ]);
        } else {
            return $this->render('articles/edit.html.twig', ['form' => $form->createView(), 'id' => $id]);
        }
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/article/delete/{id}',
        name: 'delete_article',
        requirements: ['id'=> '\d+'])]
    public function deleteAction($id, EntityManagerInterface $em, TranslatorInterface $translator) : Response {
        $article = $em->getRepository(Article::class)
            ->find($id);
        if (isset($article)){ //id exists
            //Traitement de la suppression
            $em->remove($article);
            $em->flush();
            //Message succès
            $translatedMessageDelete = $translator->trans("L'article a bien été supprimé");
            $this->addFlash('success',"$translatedMessageDelete");
            return $this->redirectToRoute('blog_show_list');
        } else {
            throw $this->createNotFoundException($translator->trans("L'article demandé ne peut pas être supprimé."));
        }
    }

    public function lastArticles($nbArticles, EntityManagerInterface $em) : Response {
        $allCategories = $em->getRepository(Category::class)->findAll();
        $lastArticles = $em->getRepository(Article::class)->findLastArticle($nbArticles);
        return $this->render('articles/lastArticles.html.twig', ['articles' => $lastArticles, 'categories'=> $allCategories]);
    }

    #[Route('/category/{id}',
        name: 'view_category',
        requirements: ['id'=> '\d+'])]
    public function categoryAction($id, EntityManagerInterface $em, TranslatorInterface $translator) : Response {
        $articles = $em->getRepository(Article::class)
            ->findByCategories($id);
        //$articles = $em->getRepository(Article::class) ->findByCategory($category);
        //Vérifie si l'article a été publié
        if ($articles === null) {
            throw $this->createNotFoundException($translator->trans("La catégorie demandée n'existe pas."));
        } else {
            return $this->render('articles/viewCategory.html.twig', [ 'id'=> $id, 'articles' => $articles]);
        }
    }
}