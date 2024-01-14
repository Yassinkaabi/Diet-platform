<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MenuRepository;
use App\Repository\RegimeRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function dashboad(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. You need to have the ROLE_ADMIN role.');
        }
        return $this->render('admin/dashboard.html.twig');
    }


    #[Route('/menu', name: 'menu')]
    public function index(MenuRepository $menuRepository, RegimeRepository $regimeRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. You need to have the ROLE_ADMIN role.');
        }
        
        return $this->render('admin/menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
            'regime' => $regimeRepository->findAll(),
        ]);
    }

    #[Route('/list/user', name: 'user')]
    public function user(UserRepository $userRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. You need to have the ROLE_ADMIN role.');
        }
        
        return $this->render('admin/users/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/Earnings', name: 'Earnings')]
    public function Earnings(MenuRepository $menuRepository, UserRepository $userRepository, ContactRepository $contactRepository, CommentRepository $commentRepository, RegimeRepository $regimeRepository): Response
    {
        $numberOfMenus = $menuRepository->countMenu();
        // $numberOfRegimes = $regimeRepository->countRegime();
        // $numberOfContacts = $contactRepository->countContact();
        // $numberOfusers = $userRepository->countUser();
        // $numberOfComments = $commentRepository->countComment();

        return $this->render('admin/dashboard.html.twig', [
            'numberOfMenus' => $numberOfMenus,
            // 'numberOfRegimes' => $numberOfRegimes,
            // 'numberOfContacts' => $numberOfContacts,
            // 'numberOfusers' => $numberOfusers,
            // 'numberOfComments' => $numberOfComments,
        ]);

}

}
