<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/author')]
class AuthorController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('', name: 'app_admin_author_index', methods: ['GET', 'POST'])]

    //version index avec pagination
    public function index(Request $request, AuthorRepository $repository): Response
    {
        $dates = [];

        if ($request->query->has('start') && ($request->query->get('start')!= '')) {
            $dates['start'] = $request->query->get('start');
        }
        
        if ($request->query->has('end') && ($request->query->get('end')!= '')) {
            $dates['end'] = $request->query->get('end');
        }
        
        if ($request->query->has('place') && ($request->query->get('place')!= '')) {
            $dates['place'] = $request->query->get('place');
        }

        $authors = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->findByDateOfBirth($dates)),
            $request->query->get(key: 'page', default:1),
            maxPerPage: 10
        );


        return $this->render('admin/author/index.html.twig', [
            'authors' => $authors,
        ]);
    }

    // page Ajouter un nouvel auteur puis éditer un auteur
    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    #[Route('/new', name: 'app_admin_author_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_author_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Author $author, Request $request, EntityManagerInterface $manager): Response
    {
        if (null === $author) {
            // seul un admin peut faire cette action :
            // $this->denyAccessUnlessGranted( attribute: 'ROLE_ADMIN' );
            // seulement si user a role édition de livre :
            $this->denyAccessUnlessGranted( 'ROLE_EDITION_DE_LIVRE' );
        }

        $author ??= new Author();
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager -> persist($author);
            $manager -> flush();

            // return $this -> redirectToRoute( route: 'app_admin_author_new');
            // return $this -> redirectToRoute( route: 'app_admin_author_index');
            return $this->redirectToRoute('app_admin_author_show', ['id' => $author->getId()]);
        }
        
        return $this->render('admin/author/new.html.twig', [
            'form' => $form,
        ]);
    }

    //page voir le detail d'un auteur
    #[Route('/{id}', name: 'app_admin_author_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Author $author): Response
    {
        return $this->render('admin/author/show.html.twig', [
            'author' => $author,
        ]);
    }
}
