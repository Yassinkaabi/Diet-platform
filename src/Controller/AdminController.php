<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MenuRepository;
use App\Repository\RegimeRepository;


#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'menu')]
    public function index(MenuRepository $menuRepository, RegimeRepository $regimeRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'menus' => $menuRepository->findAll(),
            'regime' => $regimeRepository->findAll(),
        ]);
    }

    
}
