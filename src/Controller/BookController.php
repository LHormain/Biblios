<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Enum\BookCategories;
use App\Enum\MediaTypes;
use App\Enum\CommentStatus;
use App\Form\CommentType;
use App\Repository\BookRepository;
use App\Repository\EditorRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(?MediaTypes $type, Request $request, BookRepository $repository, EditorRepository $editorRepository): Response
    {
        $filtres = [];

        $titre_en_tete = 'médias';

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

        if ($request->query->has('mediaType') && ($request->query->get('mediaType')!= '')) {
            $filtres['mediaType'] = $request->query->get('mediaType');

            if ($filtres['mediaType'] == 'book') {
                $titre_en_tete = 'livres';
            }
            elseif ($filtres['mediaType'] == 'film') {
                $titre_en_tete = 'films';
            }
            elseif ($filtres['mediaType'] == 'music') {
                $titre_en_tete = 'musiques';
            }
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
            'medias' => MediaTypes::getAssociatedArray(),
            'editors' => $editors,
            'enTete' => $titre_en_tete,
            'filtre' => $filtres['mediaType'],
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(?Book $book, ?Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {

        $comment ??= new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $today = new DateTimeImmutable();
        $status = CommentStatus::Pending;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBook($book);
            $comment->setCreatedAt($today);
            $comment->setStatus($status);
            
            $manager->persist($comment);
            $manager->flush();

            return $this->render('book/show.html.twig', [
                'book' => $book,
                'form' => $form,
            ]);
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }
}