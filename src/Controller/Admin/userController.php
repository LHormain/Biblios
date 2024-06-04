<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/user')]
class userController extends AbstractController
{
    #[Route('', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $repository, EntityManagerInterface $manager): Response
    {
        // suppression d'un utilisateur ne marche pas si un user est lier à un book
        $id = 0;
        $sup = 0;
        $role = '';

        if ($request->query->has('id') && ($request->query->get('id')!= '')) {
            $id = $request->query->get('id');
        }
        if ($request->query->has('sup') && ($request->query->get('sup')!= '')) {
            $sup = $request->query->get('sup');
        }
        if ($request->query->has('role') && ($request->query->get('role')!= '')) {
            $role = $request->query->get('role');
        }
        // récupération de l'user
        $user = $manager->getRepository(User::class)->find($id);
        if (!$user && $id != 0 ) {
            throw $this->createNotFoundException(
                'Utilisateur non trouvé '
            );
        }
        // suppression user 
        if ($id != 0 && $sup == 1) {
            $manager->remove($user);
            $manager->flush();
        }
        // ajout d'un nouveau role pour user
        if ($role == 'admin') {
            $user->setRoles(['ROLE_ADMIN']);
            $manager->flush();
        }
        elseif ($role == 'add') {
            $user->setRoles(['ROLE_AJOUT_DE_LIVRE']);
            $manager->flush();
        }
        elseif ($role == 'edit') {
            $user->setRoles(['ROLE_EDITION_DE_LIVRE']);
            $manager->flush();
        }
        elseif ($role == 'mod') {
            $user->setRoles(['ROLE_MODERATEUR']);
            $manager->flush();
        }
        elseif ($role == 'user') {
            $user->setRoles(['']);
            $manager->flush();
        }

        $users = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->createQueryBuilder('u')),
            $request->query->get(key: 'page', default:1),
            maxPerPage: 10
        );

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }
}
