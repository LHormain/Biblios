<?php

namespace App\Controller;

use App\Entity\Book;
use App\Enum\BookCategories;
use App\Repository\BookRepository;
use App\Repository\EditorRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('', name: 'app_book_index', methods: ['GET'])]
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
            // new QueryAdapter($repository->createQueryBuilder('b')),
            new QueryAdapter($repository->findByFilter($filtres)),
            $request->query->get('page', default: 1),
            maxPerPage: 12
        );

        $editors = $editorRepository->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'categories' => BookCategories::getAssociatedArray(),
            'editors' => $editors,
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}