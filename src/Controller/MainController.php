<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    // accueil site
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'Horizon Connect',
            'controller_subtitle' => 'Catalogue en ligne de la médiathèque Horizon de Villeclair',
        ]);
    }

    // page categories du catalogue
    #[Route('/catalogue', name: 'app_catalogue')]
    public function catalogue(): Response
    {
        return $this->render('main/catalogue.html.twig', 
        );
    }
}
