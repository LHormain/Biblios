<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Enum\CommentStatus;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class commentController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/comment', name: 'app_admin_comment', methods: ['GET'])]
    public function index(Request $request, CommentRepository $repository, EntityManagerInterface $manager): Response
    {
        $id = 0;
        $status = '';

        if ($request->query->has('status') && ($request->query->get('status')!= '')) {
            $status = $request->query->get('status');
        }
        if ($request->query->has('id') && ($request->query->get('id')!= '')) {
            $id = $request->query->get('id');
        }

        // mise a jour du status d'un commentaire
        $comment = $manager->getRepository(Comment::class)->find($id);
        if (!$comment && $id != 0) {
            throw $this->createNotFoundException(
                'Commentaire non trouvé pour id ='.$id
            );
        }
        if ($status == 'published') {
            $comment->setStatus(CommentStatus::Published);
            $manager->flush();    
        }
        elseif ($status == 'moderated') {
            $comment->setStatus(CommentStatus::Moderated);
            $manager->flush();    
        }
        elseif ($status == 'deleted') {
            $manager->remove($comment);
            $manager->flush();
        }

        // affichages de tous les commentaires les plus recent en premier
        $comments = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
        ),
            // new QueryAdapter($repository->findByMedia($media)),
            $request->query->get('page', default: 1),
            maxPerPage: 20
        );

        
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }
}
