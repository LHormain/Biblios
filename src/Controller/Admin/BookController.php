<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\User;
use App\Enum\BookCategories;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\EditorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

#[Route('/admin/book')]
class BookController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'app_admin_book_index', methods: ['GET'])]
    public function index(Request $request, BookRepository $repository, EditorRepository $editorRepository): Response
    {
        $filtres = [];

        if ($request->query->has('start') && ($request->query->get('start')!= '')) {
            $filtres['start'] = $request->query->get('start');
        }

        if ($request->query->has('end') && ($request->query->get('end')!= '')) {
            $filtres['end'] = $request->query->get('end');
        }

        if ($request->query->has('pays') && ($request->query->get('pays')!= '')) {
            $filtres['pays'] = $request->query->get('pays');
        }

        if ($request->query->has('category') && ($request->query->get('category')!= '')) {
            $filtres['category'] = $request->query->get('category');
        }

        if ($request->query->has('editor') && ($request->query->get('editor')!= '')) {
            $filtres['editor'] = $request->query->get('editor');
        }

        $books = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->findByFilter($filtres)),
            $request->query->get(key: 'page', default: 1),
            maxPerPage: 10
        );

        $editors = $editorRepository->findAll();
        
        return $this->render('admin/book/index.html.twig', [
            'books' => $books,
            'categories' => BookCategories::getAssociatedArray(),
            'editors' => $editors,
        ]);
    }
    
    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    #[Route('/new', name: 'app_admin_book_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_book_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(?Book $book, Request $request, EntityManagerInterface $manager): Response
    {
        if ($book) {
            // ne peut être modifier que par son créateur
            // $this->denyAccessUnlessGranted( 'book.is_creator', $book);
            // ne peut être modifié que si l'utilisateur a le role edition de livre
            $this->denyAccessUnlessGranted('ROLE_EDITION_DE_LIVRE');
        }

        $book ??= new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            if (!$book->getId() && $user instanceof User) {
                $book->setCreatedBy($user);
            }

            $manager -> persist($book);
            $manager -> flush();

            // return $this -> redirectToRoute( route: 'app_admin_book_new');
            return $this -> redirectToRoute( route: 'app_admin_book_index');
            // return $this -> redirectToRoute( 'app_admin_book_show', ['id' => $book->getId()]);
        }
        
        return $this->render('admin/book/new.html.twig', [
            'form' => $form,
        ]);
    }

    //page voir le detail d'un livre
    #[Route('/{id}', name: 'app_admin_book_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Book $book): Response
    {
        return $this->render('admin/book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
