<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\GestionBooks;
use App\Entity\User;
use App\Enum\BookCategories;
use App\Enum\BookStatus;
use App\Enum\MediaTypes;
use App\Enum\CommentStatus;
use App\Form\CommentType;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use App\Repository\EditorRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/book')]
class BookController extends AbstractController
{
    // liste des books
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

    // affichage d'un book precis avec commentaires
    #[Route('/{id}', name: 'app_book_show', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(?Book $book, ?Comment $comment, Request $request, EntityManagerInterface $manager, CommentRepository $repository): Response
    {
        // formulaire pour commentaires. tous les visiteurs peuvent commenter
        // pour une version ou seul les visiteurs inscrits peuvent commenter faire une page similaire à celle pour emprunt book
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


        }

        $media['book'] = $book->getId();
        $media['status'] = 'Published'; // pour affichage commentaires

        $comments = Pagerfanta::createForCurrentPageWithMaxPerPage(
            // new QueryAdapter($repository->createQueryBuilder('c')),
            new QueryAdapter($repository->findByMedia($media)),
            $request->query->get('page', default: 1),
            maxPerPage: 4
        );

        
        
        return $this->render('book/show.html.twig', [
            'book' => $book,
            'form' => $form,
            'comments' => $comments,
        ]);
    }

    //changer le status du livre en emprunté
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/edit', name: 'app_book_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(?Book $book, Request $request, EntityManagerInterface $manager): Response
    {

        $statusBook = '';
        $today = new DateTimeImmutable();

        if ($request->query->has('status') && ($request->query->get('status')!= '')) {
            $statusBook = $request->query->get('status');
        }

        if ($statusBook != '') {
            // change le status du book dans Book
            $book->setStatus(BookStatus::Borrowed);
            $manager->flush();
            // enregistre le couple book user et la date du jour dans GestionBook
            $gestion = new GestionBooks();
            $gestion->setBook($book);
            $user = $this->getUser();
            if (!$gestion->getId() && $user instanceof User) {
                $gestion->setUser($user);
            }
            $gestion->setDateSortie($today);

            $manager->persist($gestion);
            $manager->flush();
        }

        // redirige sur la page du livre
        return $this->redirectToRoute('app_book_show', [
            'id' => $book->getId(),
        ]);
    }
}