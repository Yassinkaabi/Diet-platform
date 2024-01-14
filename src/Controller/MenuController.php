<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Regime;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use App\Repository\RegimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/menu', name: 'menu_')]
class MenuController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(MenuRepository $menuRepository, RegimeRepository $regimeRepository): Response
    {
        return $this->render('menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
            'regime' => $regimeRepository->findAll(),
        ]);
    }

    // #[Route('/latest-menu', name: 'latest')]
    // public function latestMenu(MenuRepository $menuRepository, EntityManagerInterface $entityManager): Response
    // {
    //     // $menuRepository = $entityManager->getRepository(Menu::class);
    //     $latestMenu = $menuRepository->findLatestMenu();
    //     return $this->render('menu/show.html.twig', [
    //         'latestMenus' => $latestMenu,
    //     ]);
    // }

    #[Route('/add', name: 'add')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    { {
            if (!$this->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException('Access denied. You need to have the ROLE_ADMIN role.');
            }

            $menu = new Menu();
            $form = $this->createForm(MenuType::class, $menu);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($menu);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le menu a été ajoutée avec succès. '
                );
                return $this->redirectToRoute('app_admin_menu');
            }

            return $this->render('admin/menu/new.html.twig', [
                'menu' => $menu,
                'form' => $form,
            ]);
        }
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Menu $menu, Request $request, EntityManagerInterface $manager): Response
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. You need to have the ROLE_ADMIN role.');
        }

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($menu);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le menu a été modifier avec succès. '
            );
            return $this->redirectToRoute('app_admin_menu');
        }

        return $this->render('admin/menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('admin/{id}', name: 'show_admin')]
    public function showAdmin(Menu $menu, RegimeRepository $regimeRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. You need to have the ROLE_ADMIN role.');
        }

        return $this->render('admin/menu/show.html.twig', [
            'menu' => $menu,
            'regime' => $regimeRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(Menu $menu, RegimeRepository $regimeRepository, MenuRepository $menuRepository): Response
    {
        $latestMenu = $menuRepository->findLatestMenus();
        return $this->render('menu/show.html.twig', [
            'latestMenus' => $latestMenu,
            'menu' => $menu,
            'regime' => $regimeRepository->findAll(),
        ]);
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete(MenuRepository $menuRepository, EntityManagerInterface $entityManager, $id): Response
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. You need to have the ROLE_ADMIN role.');
        }

        $menuRepository = $entityManager->getRepository(Menu::class);
        $menu = $menuRepository->find($id);
        // $id = $menu->getId();
        // $r = $menuRepository->find($id);
        // dd($r);
        if (!$menu) {
            throw $this->createNotFoundException('No menu found for id ' . $id);
        }
        $entityManager->remove($menu);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le menu a été supprimé avec succès. '
        );
        return $this->redirectToRoute('menu_index');
    }

    #[Route('/add-to-favorites/{id}', name: 'add_to_favorites')]
    public function addToFavorites(Menu $menu, EntityManagerInterface $em, Request $request): Response
    {
        
        $user = $this->getUser();

        if ($user) {
            $user->addFavoriteMenu($menu);
            $em->flush();
        }
        return $this->redirectToRoute('menu_show', ['id' => $menu->getId()]);
    }
}
