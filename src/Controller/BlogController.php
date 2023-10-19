<?php

namespace App\Controller;


use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog', name: 'blog_')]
class BlogController extends AbstractController {

    #[Route('/list/{page}',
        name: 'show_list',
        requirements: ['page' => '\d+'],
        defaults: ['page' => 1])]
    public function listAction($page, EntityManagerInterface $em): Response {

        if ($page < 1) {
            throw new NotFoundHttpException("La page $page n'existe pas.");
        }

        $articlesPerPage = $this->getParameter('my_parameter');
        $articles = $em->getRepository(Article::class)->findAllWithPaging($page, $articlesPerPage);

        $article = count($articles);
        $nbTotalPages = intval(ceil($article/$articlesPerPage));
        //dump($articles);
        //$articles = $em->getRepository(Article::class)->findBy([],['createdAt'=>'DESC']);
        if($page > $nbTotalPages){
            throw $this->createNotFoundException("La page demandée n'existe pas");
        }
        return $this->render('articles/list.html.twig', ['articles' => $articles, 'page'=>$page, 'totalPages'=>$nbTotalPages]);
    }


    #[Route('/article/{id}',
        name: 'view_article',
        requirements: ['id'=> '\d+'])]
    public function viewAction($id, EntityManagerInterface $em) : Response {
        $article = $em->getRepository(Article::class)
            ->find($id);
        //Vérifie si l'article a été publié
        $published = $article->isPublished();
        if (($article === null) && ($published ==null) ) {
            throw new NotFoundHttpException("L'article demandé n'existe pas.");
        } else {
        return $this->render('articles/view.html.twig', [ 'id'=> $id, 'articles' => $article]);
        }
    }

    //addAction : URL : /blog/article/add. Faire un if principal (tester manuellement avec if(true) et if(false)) :
    // • Si on est en train de valider le formulaire (on verra plus tard comment faire)
    // ⇒ message flash de confirmation + redirection vers la liste
    // • Sinon : affichage du formulaire
    #[Route('/article/add',
        name: 'add_article')]
    public function addAction(EntityManagerInterface $em) : Response {

        if (false){ //form is submitted && valid
            //Traitement formulaire

            $message = "L'article a bien été ajouté";
            //Message succès
            $this->addFlash('success',$message);
            return $this->redirectToRoute('blog_view_article', ['id'=>1, 'message'=> $message]);

        } else {
            $article = new Article();
            $article->setTitle('Premier article')->setContent('Contenu du premier article')->setAuthor('Moi')
                ->setPublished('True')->setCreatedAt(new \DateTime('2023/10/16'))->setNbViews(4)
                ->setUpdatedAt(new \DateTime('2023/10/17'));
            $em->persist($article);
            $em->flush();

            return $this->render('articles/add.html.twig');
        }
    }

    #[Route('/article/edit/{id}',
        name: 'edit_article',
        requirements: ['id'=> '\d+'])
    ]
    public function editAction($id, EntityManagerInterface $em) : Response {
        $article = $em->getRepository(Article::class)
            ->find($id);
        if (!$article){ //form is submitted && valid
            //Traitement de l'édition

            //Message succès
            $this->addFlash('info',"L'article a bien été modifié");
            return $this->redirectToRoute('blog_view_article', [ 'id'=> $id ]);
        }
            return $this->render('articles/edit.html.twig', ['id' => $id]);
    }

    #[Route('/article/delete/{id}',
        name: 'delete_article',
        requirements: ['id'=> '\d+'])]
    public function deleteAction($id, EntityManagerInterface $em) : Response {
        $article = $em->getRepository(Article::class)
            ->find($id);
        if (isset($article)){ //id exists
            //Traitement de la suppression
            $em->remove($article);
            $em->flush();
            //Message succès
            $this->addFlash('success',"L'article a bien été supprimé");
            return $this->redirectToRoute('blog_show_list');
        } else {
            throw new NotFoundHttpException("L'article demandé ne peut pas être supprimer.");
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
    public function categoryAction($id, EntityManagerInterface $em) : Response {
        $articles = $em->getRepository(Article::class)
            ->findByCategories($id);
        //$articles = $em->getRepository(Article::class) ->findByCategory($category);
        //Vérifie si l'article a été publié

        if (($articles === null)) {
            throw new NotFoundHttpException("La catégorie demandée n'existe pas.");
        } else {
            return $this->render('articles/viewCategory.html.twig', [ 'id'=> $id, 'articles' => $articles]);
        }
    }
}