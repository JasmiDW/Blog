<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {

    #[Route('/{_locale}', name : 'home') ]
    public function homeAction() : Response {
        return $this->render('base.html.twig');
    }

    #[Route('', name : 'project') ]
    public function projectAction() : Response {
        return $this->render('base.html.twig');
    }


    #[Route ('/{_locale}/a-propos', name: 'a_propos')]
    public function aboutAction() : Response {
        return $this->render('about.html.twig');
    }
}