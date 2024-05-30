<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class mainController extends AbstractController
{
    #[Route('/admin/main', name: 'app_admin_main')]
    public function index(): Response
    {
        return $this->render('admin/main/index.html.twig');
    }
}
