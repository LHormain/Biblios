<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\GestionBooks;
use App\Entity\User;
use App\Enum\BookCategories;
use App\Enum\BookStatus;
use App\Enum\MediaTypes;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\EditorRepository;
use DateTimeImmutable;
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
    // index des livres avec pagination et fonction recherche
    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'app_admin_book_index', methods: ['GET'])]
    public function indexbook(Request $request, BookRepository $repository, EditorRepository $editorRepository): Response
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
            new QueryAdapter($repository->findByFilter($filtres)),
            $request->query->get(key: 'page', default: 1),
            maxPerPage: 10
        );

        $editors = $editorRepository->findAll();
        
        return $this->render('admin/book/index.html.twig', [
            'books' => $books,
            'categories' => BookCategories::getAssociatedArray(),
            'medias' => MediaTypes::getAssociatedArray(),
            'editors' => $editors,
            'enTete' => $titre_en_tete,
        ]);
    }
    
    // ajout et edition de livre
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

    // page gestion des livres empruntés et indisponible
    #[IsGranted('ROLE_MODERATEUR')]
    #[Route('/book-gestion', name: 'app_admin_book_gestion', methods: ['GET'])]
    public function borrowed(Request $request, BookRepository $repository, EntityManagerInterface $manager): Response
    {
        // changement de status d'un book
        $id = 0;
        $status = '';
        $today = new DateTimeImmutable();

        if ($request->query->has('etat') && ($request->query->get('etat')!= '')) {
            $status = $request->query->get('etat');
        }

        if ($request->query->has('id') && ($request->query->get('id')!= '')) {
            $id = $request->query->get('id');
        }

        // récupère le livre a modifier
        $book = $manager->getRepository(Book::class)->find($id);
        if (!$book && $id != 0) {
            throw $this->createNotFoundException(
                'média non trouvé pour id ='.$id
            );
        }
        if ($status == 'available') {
            // change le status du book
            $book->setStatus(BookStatus::Available);
            $manager->flush();   
            // change la date de GestionBooks pour dire quand le livre est retourné en rayon
            $gestionbook = $manager->getRepository(GestionBooks::class)->findBy(['book' => $id], ['DateSortie' => 'DESC']);
            if (isset($gestionbook[0])) {
                $gestionbook[0] -> setDateRentre($today);
                $manager->flush();    
            }
        }
        elseif ($status == 'unavailable') {
            // change le status
            $book->setStatus(BookStatus::Unavailable);
            $manager->flush();   

            // // ajoute a gestionBooks
            // $gestion = new GestionBooks();
            // $gestion->setBook($book);
            // // définie qui a rendu le livre indisponible
            // $user = $this->getUser();
            // if (!$gestion->getId() && $user instanceof User) {
            //     $gestion->setUser($user);
            // }
            // $gestion->setDateSortie($today);

            // $manager->persist($gestion);
            // $manager->flush();
        }

        // création de la liste à partir du filtre
        $filtres = [];
        $titre_en_tete = 'médias';
        $status_en_tete = 'empruntés ou indisponibles';

        // récupération status pour filtre
        if ($request->query->has('status') && ($request->query->get('status')!= '')) {
            $filtres['status'] = $request->query->get('status');
            if ($filtres['status'] == 'borrowed') {
                $status_en_tete = 'Empruntés';
            }
            elseif ($filtres['status'] == 'unavailable') {
                $status_en_tete = 'Indisponibles';
            }
        }
        else {
            $filtres['status'] = 'available';
        }

        // récupération type de média pour filtre
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

        // récupération de la liste de book
        $books = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->findByFilter($filtres)),
            $request->query->get(key: 'page', default: 1),
            maxPerPage: 10
        );

        // affichage
        return $this->render('admin/book/gestion.html.twig', [
            'books' => $books,
            'medias' => MediaTypes::getAssociatedArray(),
            'enTete' => $titre_en_tete,
            'status' => $status_en_tete,
        ]);
    }
}
